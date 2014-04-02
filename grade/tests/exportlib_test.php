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
 * Unit tests for grade/export/lib.php.
 *
 * @package   core_grades
 * @category  test
 * @copyright 2014 Gilles-Philippe Leblanc <gilles-philippe.leblanc@umontreal.ca>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/grade/lib.php');
require_once($CFG->dirroot.'/grade/export/lib.php');

/**
 * A test class used to test grade_export, the abstract grade export parent class.
 *
 * @copyright 2014 Gilles-Philippe Leblanc <gilles-philippe.leblanc@umontreal.ca>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
class grade_export_test extends grade_export {

    /**
     * Implementation of the abstract method print_grades().
     */
    public function print_grades() {
    }
}

/**
 * Tests grade_report, the parent class for all grade reports.
 *
 * @copyright 2014 Gilles-Philippe Leblanc <gilles-philippe.leblanc@umontreal.ca>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
class gradeexportlib_testcase extends advanced_testcase {

    /**
     * @var grade_grade The grade_grade object used in the tests.
     */
    protected $gradegrade;

    /**
     * @var grade_export_test The grade_export_test instance used in the tests.
     */
    protected $gradeexport;

    /**
     * @var stdClass The the user object of the student used in the tests.
     */
    protected $student;

    /**
     * @var grade_item The grade item of the activity used in the tests.
     */
    protected $gradeitem;

    /**
     * Setup test data.
     */
    protected function setUp() {
        global $DB;
        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();
        $this->student = $this->getDataGenerator()->create_user();

        $role = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($this->student->id, $course->id, $role->id);

        // Create an activity.
        $assign = $this->getDataGenerator()->create_module('assign', array('assessed' => 1, 'scale' => 100,
                'course' => $course->id));

        // Store the activity grade item who will be used for grading.
        $this->gradeitem = grade_item::fetch(array('itemtype' => 'mod', 'itemmodule' => 'assign', 'iteminstance' => $assign->id,
                'courseid' => $course->id));

        $this->gradeexport = new grade_export_test($course);
    }

    /**
     * Insert an empty grade to the assignment activity for the test student.
     */
    protected function insert_empty_grade_assign() {
        $this->gradegrade = new grade_grade();
        $this->gradegrade->itemid = $this->gradeitem->id;
        $this->gradegrade->userid = $this->student->id;
        $this->gradegrade->rawgrademax = 100;
        $this->gradegrade->rawgrademin = 0;
        $this->gradegrade->timecreated = time();
        $this->gradegrade->timemodified = $this->gradegrade->timecreated;
        $this->gradegrade->insert();
    }

    /**
     * Grade the assignment activity for the test student.
     */
    protected function grade_assign() {
        $assigngrade = 85.12;
        $this->gradegrade->rawgrade = $assigngrade;
        $this->gradegrade->finalgrade = $assigngrade;
        $this->gradegrade->timecreated = time();
        $this->gradegrade->timemodified = time();
        $this->gradegrade->exported = $this->gradegrade->timemodified;
        $this->gradegrade->update();
    }

    /**
     * Test format_grade method.
     */
    public function test_format_grade() {

        $this->insert_empty_grade_assign();

        // Test with an empty grade.
        $this->assertEquals('-', $this->gradeexport->format_grade($this->gradegrade));
        $this->assertEquals('-', $this->gradeexport->format_grade($this->gradegrade, GRADE_DISPLAY_TYPE_LETTER));

        $this->grade_assign();

        // Test after grading.
        $this->assertEquals('85.12', $this->gradeexport->format_grade($this->gradegrade));
        $this->assertEquals('B', $this->gradeexport->format_grade($this->gradegrade, GRADE_DISPLAY_TYPE_LETTER));

        // Test with a different display type.
        $this->gradeexport->displaytype = GRADE_DISPLAY_TYPE_REAL_LETTER;
        $this->assertEquals('85.12 (B)', $this->gradeexport->format_grade($this->gradegrade));
    }

    /**
     * Test generate_preview_user_grade method.
     */
    public function test_generate_preview_user_grade() {

        $this->insert_empty_grade_assign();
        $this->grade_assign();

        $geub = new grade_export_update_buffer();

        // Check the preview user grade with a normal behavior.
        $strgrade = $this->gradeexport->format_grade($this->gradegrade);
        $expected = html_writer::tag('td', $strgrade);
        $actual = $this->gradeexport->generate_preview_user_grade($this->student, $this->gradeitem, $strgrade, $geub);

        $this->assertEquals($expected, $actual);

        // Check the preview user grade with the updatedgradesonly activated.
        $this->gradeexport->updatedgradesonly = true;
        $expected = html_writer::tag('td', get_string('unchangedgrade', 'grades'));
        $actual = $this->gradeexport->generate_preview_user_grade($this->student, $this->gradeitem, $strgrade, $geub);

        $this->assertEquals($expected, $actual);
    }

    /**
     * Test get_export_params method.
     */
    public function test_get_export_params() {

        $expectedparams = array('id' => $this->gradeexport->course->id,
                                'groupid' => 0,
                                'itemids' => implode(',', array_keys($this->gradeexport->columns)),
                                'export_letters' => null,
                                'export_feedback' => false,
                                'updatedgradesonly' => false,
                                'displaytype' => GRADE_DISPLAY_TYPE_REAL,
                                'decimalpoints' => 2,
                                'export_onlyactive' => false,
                                'usercustomfields' => false,
                                'includecoursetotalletter' => false);

        // Check default behavior.
        $actualparams = $this->gradeexport->get_export_params();
        $this->assertEquals($expectedparams, $actualparams);

        // Check a different group.
        $this->gradeexport->groupid = 1;
        $expectedparams['groupid'] = 1;
        $actualparams = $this->gradeexport->get_export_params();
        $this->assertEquals($expectedparams, $actualparams);

        // Check with no grade item selected for export.
        $this->gradeexport->columns = array();
        $expectedparams['itemids'] = '-1';
        $actualparams = $this->gradeexport->get_export_params();
        $this->assertEquals($expectedparams, $actualparams);

        // Check with the letters exported.
        $this->gradeexport->export_letters = true;
        $expectedparams['export_letters'] = true;
        $actualparams = $this->gradeexport->get_export_params();
        $this->assertEquals($expectedparams, $actualparams);

        // Check with the feedback exported.
        $this->gradeexport->export_feedback = true;
        $expectedparams['export_feedback'] = true;
        $actualparams = $this->gradeexport->get_export_params();
        $this->assertEquals($expectedparams, $actualparams);

        // Check with the updated grades only.
        $this->gradeexport->updatedgradesonly = true;
        $expectedparams['updatedgradesonly'] = true;
        $actualparams = $this->gradeexport->get_export_params();
        $this->assertEquals($expectedparams, $actualparams);

        // Check with a different display type.
        $this->gradeexport->displaytype = GRADE_DISPLAY_TYPE_LETTER;
        $expectedparams['displaytype'] = GRADE_DISPLAY_TYPE_LETTER;
        $actualparams = $this->gradeexport->get_export_params();
        $this->assertEquals($expectedparams, $actualparams);

        // Check with a different decimal points.
        $this->gradeexport->decimalpoints = 0;
        $expectedparams['decimalpoints'] = 0;
        $actualparams = $this->gradeexport->get_export_params();
        $this->assertEquals($expectedparams, $actualparams);

        // Check with active grades only.
        $this->gradeexport->onlyactive = true;
        $expectedparams['export_onlyactive'] = true;
        $actualparams = $this->gradeexport->get_export_params();
        $this->assertEquals($expectedparams, $actualparams);

        // Check with user custom fields.
        $this->gradeexport->usercustomfields = true;
        $expectedparams['usercustomfields'] = true;
        $actualparams = $this->gradeexport->get_export_params();
        $this->assertEquals($expectedparams, $actualparams);

        // Check including the course total letter.
        $this->gradeexport->includecoursetotalletter = true;
        $expectedparams['includecoursetotalletter'] = true;
        $actualparams = $this->gradeexport->get_export_params();
        $this->assertEquals($expectedparams, $actualparams);
    }
}
