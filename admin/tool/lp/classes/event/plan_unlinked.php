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
 * Plan unlinked event.
 *
 * @package    tool_lp
 * @copyright  2016 Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_lp\event;

use core\event\base;
use tool_lp\plan;

defined('MOODLE_INTERNAL') || die();

/**
 * Plan unlinked event class.
 *
 *
 * @package    tool_lp
 * @since      Moodle 3.1
 * @copyright  2016 Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class plan_unlinked extends base {

    /**
     * Convenience method to instantiate the plan unlinked event.
     *
     *
     * @param plan $plan The plan.
     * @return self
     */
    public static final function create_from_plan(plan $plan) {
        if (!$plan->get_id()) {
            throw new \coding_exception('The plan ID must be set.');
        }
        $event = static::create(array(
            'contextid'  => $plan->get_context()->id,
            'objectid' => $plan->get_id(),
            'relateduserid' => $plan->get_userid(),
        ));
        $event->add_record_snapshot(plan::TABLE, $plan->to_record());
        return $event;
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' unlinked the learning plan with id '$this->objectid'.";
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventplanunlinked', 'tool_lp');
    }

    /**
     * Get URL related to the action
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/admin/tool/lp/plan.php',
                               array('id' => $this->objectid, 'pagecontextid' => $this->contextid));
    }

    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->data['objecttable'] = plan::TABLE;
    }

    /**
     * Get_objectid_mapping method.
     *
     * @return string the name of the restore mapping the objectid links to
     */
    public static function get_objectid_mapping() {
        return base::NOT_MAPPED;
    }

}