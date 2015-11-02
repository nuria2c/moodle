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
 * Class for plan_competency persistence.
 *
 * @package    tool_lp
 * @copyright  2015 Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lp;

use lang_string;

/**
 * Class for managing competencies in the plan (add/remove competencies for given plan).
 *
 * @copyright  2015 Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class plan_competency extends persistent {

    /** Table name for plan_competency persistency */
    const TABLE = 'tool_lp_plan_competency';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return array(
            'planid' => array(
                'type' => PARAM_INT,
            ),
            'competencyid' => array(
                'type' => PARAM_INT,
            ),
            'sortorder' => array(
                'type' => PARAM_INT,
                'default' => null,
            ),
        );
    }

    /**
     * Count the plans using a competency.
     *
     * @param int $competencyid The competency id
     * @return int
     */
    public static function count_plans($competencyid) {
        global $DB;

        $sql = 'SELECT COUNT(plan.id)
                  FROM {' . self::TABLE . '} plancomp
                  JOIN {' . plan::TABLE . '} plan
                    ON plancomp.planid = plan.id
                 WHERE plancomp.competencyid = ? ';
        $params = array($competencyid);

        $results = $DB->count_records_sql($sql, $params);

        return $results;
    }

    /**
     * Count the competencies using a plan.
     *
     * @param int $planid The plan id
     * @return int
     */
    public static function count_competencies($planid) {
        global $DB;

        $sql = 'SELECT COUNT(comp.id)
                  FROM {' . self::TABLE . '} plancomp
                  JOIN {' . competency::TABLE . '} comp
                    ON plancomp.competencyid = comp.id
                 WHERE plancomp.planid = ? ';
        $params = array($planid);

        $results = $DB->count_records_sql($sql, $params);

        return $results;
    }

    /**
     * List the plans using a competency.
     *
     * @param int $competencyid The competency id
     * @return array[plan]
     */
    public static function list_plans($competencyid) {
        global $DB;

        $sql = 'SELECT plan.*
                  FROM {' . plan::TABLE . '} plan
                  JOIN {' . self::TABLE . '} plancomp
                    ON plancomp.planid = plan.id
                 WHERE plancomp.competencyid = ? ';
        $params = array($competencyid);

        $results = $DB->get_records_sql($sql, $params);

        $instances = array();
        foreach ($results as $result) {
            array_push($instances, new plan(0, $result));
        }

        return $instances;
    }

    /**
     * List the competencies in this plan.
     *
     * @param int $planid The plan id
     * @return array[competency]
     */
    public static function list_competencies($planid) {
        global $DB;

        $sql = 'SELECT comp.*
                  FROM {' . competency::TABLE . '} comp
                  JOIN {' . self::TABLE . '} plancomp
                    ON plancomp.competencyid = comp.id
                 WHERE plancomp.planid = ?
              ORDER BY plancomp.sortorder ASC';
        $params = array($planid);

        $results = $DB->get_records_sql($sql, $params);

        $instances = array();
        foreach ($results as $result) {
            array_push($instances, new competency(0, $result));
        }

        return $instances;
    }

    /**
     * Hook to execute before validate.
     *
     * @return void
     */
    protected function before_validate() {
        if ($this->get_sortorder() === null) {
            $this->set_sortorder($this->count_records(array('planid' => $this->get_planid())));
        }
    }

    /**
     * Validate competencyid.
     *
     * @param  int $value ID.
     * @return true|lang_string
     */
    protected function validate_competencyid($value) {
        if (!competency::record_exists($value)) {
            return new lang_string('invaliddata', 'error');
        }
        return true;
    }

    /**
     * Validate planid.
     *
     * @param  int $value ID.
     * @return true|lang_string
     */
    protected function validate_planid($value) {
        if (!plan::record_exists($value)) {
            return new lang_string('invaliddata', 'error');
        }
        return true;
    }

}
