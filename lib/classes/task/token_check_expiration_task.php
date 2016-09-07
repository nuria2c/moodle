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
 * A scheduled task.
 *
 * @package    core
 * @copyright  2016 Steve Massicotte
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Steve Massicotte
 */
namespace core\task;

/**
 * Simple task to run a check the expiration of token and send a notification to administrator.
 *
 * @copyright  2016 Steve Massicotte
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Steve Massicotte
 */
class token_check_expiration_task extends scheduled_task {
    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('tasktokencheckexpiration', 'admin');
    }

    /** @var array Array of the dates of each token that as expire */
    private $tokensexpirationdates;

    /**
     * Do the job.
     * Throw exceptions on errors (the job will be retried).
     */
    public function execute() {
        global $DB;

        $date2weekinfutur = strtotime('+2 week');

        $tokens = $DB->get_records('external_tokens', array('tokentype' => EXTERNAL_TOKEN_PERMANENT));

        $this->tokensexpirationdates = array();
        foreach ($tokens as $token) {
            if (!empty($token->validuntil) && $date2weekinfutur > $token->validuntil) {
                $this->tokensexpirationdates[] = $token->validuntil;
                mtrace('A token is near is expiration : '
                        . date('Y-m-d H:i:s', $token->validuntil));
            }
        }

        if (!empty($this->tokensexpirationdates)) {
            $this->tokensexpirationdates = array_unique($this->tokensexpirationdates);
            self::send_notification_to_admin();
        }
    }

    /**
     * Send a notification to the administrator.
     *
     * @return bool|string Returns "true" if mail was sent OK and "false" if there was an error
     */
    private function send_notification_to_admin() {
        global $CFG;

        $site = get_site();

        $subject = get_string('tasktokencheckexpirationsubject', 'admin', format_string($site->fullname));

        $supportuser = \core_user::get_support_user();

        $a = new \stdClass();
        $a->sitename = format_string($site->fullname);
        $a->supportname = $CFG->supportname;
        $a->link = $CFG->wwwroot . '/admin/settings.php?section=webservicetokens';
        $a->signoff = generate_email_signoff();

        $a->datestr = "";
        foreach ($this->tokensexpirationdates as $tokenexpirationdate) {
            $a->datestr .= "\n- " . date('Y-m-d H:i:s', $tokenexpirationdate);
        }

        $message = get_string('tasktokencheckexpirationmessage', 'admin', $a);

        if (!empty($CFG->noemailever)) {
            mtrace($subject);
            mtrace($message);
            return true;
        }

        return email_to_user($supportuser, $supportuser, $subject, $message);
    }

}
