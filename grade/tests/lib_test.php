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
 * Core grading tests.
 *
 * @package    core_grading
 * @category   test
 * @copyright  2014 Gilles-Philippe Leblanc <gilles-philippe.leblanc@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/grade/lib.php');

/**
 * Core grading tests class.
 *
 * @package    core_grading
 * @category   test
 * @copyright  2014 Gilles-Philippe Leblanc <gilles-philippe.leblanc@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_grading_lib_testcase extends advanced_testcase {

    /** @var stdClass The course used in the tests. */
    protected $course;

    /** @var stdClass The editing teacher account used in the tests. */
    protected $teacher;

    /** @var stdClass The student account used in the tests. */
    protected $student;

    /**
     * Setup test data.
     */
    protected function setUp() {
        global $DB;

        $this->resetAfterTest();

        // Create a course.
        $this->course = $this->getDataGenerator()->create_course();

        // Create users and enrol them in the course.
        $this->teacher = $this->getDataGenerator()->create_user();
        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        $this->getDataGenerator()->enrol_user($this->teacher->id, $this->course->id, $teacherrole->id);
        $this->student = $this->getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($this->student->id, $this->course->id, $studentrole->id);
    }

    /**
     * Test get_plugins_import
     */
    public function test_get_plugins_import() {

        // Assert the default behaviour for teachers, all plugins are in alphabetical order.
        $this->setUser($this->teacher);
        set_config('grade_import_customsorting', '');
        $pluginlist = array_keys(core_component::get_plugin_list('gradeimport'));
        sort($pluginlist);
        $importplugins = array_keys(grade_helper::get_plugins_import($this->course->id, true));
        $this->assertEquals($pluginlist, $importplugins);

        // Check it with the modified plugin custom sort order for teachers.
        set_config('grade_import_customsorting', 'xml,csv');
        unset($pluginlist[array_search('xml', $pluginlist)]);
        unset($pluginlist[array_search('csv', $pluginlist)]);
        array_unshift($pluginlist, "xml", "csv");
        $importplugins = array_keys(grade_helper::get_plugins_import($this->course->id, true));
        $this->assertEquals($pluginlist, $importplugins);

        // Same test with the gradepublishing config turned on.
        set_config('gradepublishing', true);
        array_splice($pluginlist, 2, 0, "keymanager");
        $importplugins = array_keys(grade_helper::get_plugins_import($this->course->id, true));
        $this->assertEquals($pluginlist, $importplugins);

        // Assert the default behaviour for students, it should return false.
        $this->setUser($this->student);
        $importplugins = grade_helper::get_plugins_import($this->course->id, true);
        $this->assertFalse($importplugins);
    }

    /**
     * Test get_plugins_export
     */
    public function test_get_plugins_export() {

        // Check the default behaviour for teachers.
        $this->setUser($this->teacher);
        set_config('grade_export_customsorting', '');
        $pluginlist = array_keys(core_component::get_plugin_list('gradeexport'));
        sort($pluginlist);
        $exportplugins = array_keys(grade_helper::get_plugins_export($this->course->id, true));
        $this->assertEquals($pluginlist, $exportplugins);

        // Check it with the modified plugin custom sort order for teachers.
        set_config('grade_export_customsorting', 'xml,txt');
        unset($pluginlist[array_search('xml', $pluginlist)]);
        unset($pluginlist[array_search('txt', $pluginlist)]);
        array_unshift($pluginlist, "xml", "txt");
        $exportplugins = array_keys(grade_helper::get_plugins_export($this->course->id, true));
        $this->assertEquals($pluginlist, $exportplugins);

        // Same test with the gradepublishing config turned on.
        set_config('gradepublishing', true);
        array_splice($pluginlist, 2, 0, "keymanager");
        $exportplugins = array_keys(grade_helper::get_plugins_export($this->course->id, true));
        $this->assertEquals($pluginlist, $exportplugins);

        // The students should not see any plugins.
        $this->setUser($this->student);
        $exportplugins = grade_helper::get_plugins_export($this->course->id, true);
        $this->assertFalse($exportplugins);
    }
}
