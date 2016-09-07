<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This file contains the unittests for token_check_expiration_task.
 *
 * @package   core
 * @category  test
 * @copyright 2016 Steve Massicotte
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Steve Massicotte
 */

defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/fixtures/task_fixtures.php');

/**
 * Test class for token check expiration task.
 *
 * @package core
 * @category task
 * @copyright 2016 Steve Massicotte
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class token_check_expiration_task_testcase extends advanced_testcase {

    /** @var array Id of the dummy external service */
    protected $externalserviceid;

    /**
     * Setup function to create things needed for the tests.
     */
    public function setUp() {
        parent::setUp();

        global $DB;

        // Set current user.
        $user = array();
        $user['username'] = 'johnd';
        $user['firstname'] = 'John';
        $user['lastname'] = 'Doe';
        self::setUser(self::getDataGenerator()->create_user($user));

        // Add a web service and token.
        $webservice = new stdClass();
        $webservice->name = 'Test web service';
        $webservice->enabled = true;
        $webservice->restrictedusers = false;
        $webservice->component = 'moodle';
        $webservice->timecreated = time();
        $webservice->downloadfiles = true;
        $webservice->uploadfiles = true;
        $this->externalserviceid = $DB->insert_record('external_services', $webservice);
    }
    /**
     * Test basic token_check_expiration_task task execution.
     */
    public function test_token_check_expiration_task() {
        $this->resetAfterTest(true);

        $task = new \core\task\token_check_expiration_task();

        $sink = $this->redirectEmails();
        $task->execute();
        $this->assertCount(0, $sink->get_messages());

        $this->create_token('testtoken1', strtotime('+3 week'));
        $task->execute();
        $this->assertCount(0, $sink->get_messages());

        $this->create_token('testtoken1', time());
        $task->execute();
        $this->assertCount(1, $sink->get_messages());
        $sink->close();
    }

    /**
     * Function to create token.
     * @param string $tokenstr The content of the token
     * @param timestamp $validuntil The date when the token will not be usable anymore
     */
    private function create_token($tokenstr, $validuntil) {
        global $DB, $USER;

        $externaltoken = new stdClass();
        $externaltoken->token = $tokenstr;
        $externaltoken->tokentype = EXTERNAL_TOKEN_PERMANENT;
        $externaltoken->userid = $USER->id;
        $externaltoken->externalserviceid = $this->externalserviceid;
        $externaltoken->contextid = 1;
        $externaltoken->creatorid = $USER->id;
        $externaltoken->timecreated = time();
        $externaltoken->validuntil = $validuntil;
        $DB->insert_record('external_tokens', $externaltoken);
    }
}
