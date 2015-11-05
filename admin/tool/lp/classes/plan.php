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
 * Class for plans persistence.
 *
 * @package    tool_lp
 * @copyright  2015 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lp;

use context_user;
use dml_missing_record_exception;
use lang_string;

/**
 * Class for loading/storing plans from the DB.
 *
 * @copyright  2015 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class plan extends persistent {

    const TABLE = 'tool_lp_plan';

    /** Draft status */
    const STATUS_DRAFT = 0;

    /** Active status */
    const STATUS_ACTIVE = 1;

    /** Complete status */
    const STATUS_COMPLETE = 2;

    /** @var plan Object before update. */
    protected $beforeupdate = null;

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return array(
            'name' => array(
                'type' => PARAM_TEXT,
            ),
            'description' => array(
                'type' => PARAM_TEXT,
                'default' => ''
            ),
            'descriptionformat' => array(
                'choices' => array(FORMAT_HTML, FORMAT_MOODLE, FORMAT_PLAIN, FORMAT_MARKDOWN),
                'type' => PARAM_INT,
                'default' => FORMAT_HTML,
            ),
            'userid' => array(
                'type' => PARAM_INT,
            ),
            'templateid' => array(
                'type' => PARAM_INT,
                'default' => null,
                'null' => NULL_ALLOWED,
            ),
            'status' => array(
                'choices' => array(self::STATUS_DRAFT, self::STATUS_COMPLETE, self::STATUS_ACTIVE),
                'type' => PARAM_INT,
                'default' => self::STATUS_DRAFT,
            ),
            'duedate' => array(
                'type' => PARAM_INT,
                'default' => 0,
            ),
        );
    }

    /**
     * Hook to execute before an update.
     *
     * @param bool $result Whether or not the update was successful.
     * @return void
     */
    protected function before_update() {
        $this->beforeupdate = new self($this->get_id());
    }

    /**
     * Hook to execute after an update.
     *
     * @param bool $result Whether or not the update was successful.
     * @return void
     */
    protected function after_update($result) {
        if ($result) {
            // Archive user competencies if the status of the plan is changed to complete.
            $mustarchivecompetencies = $this->get_status() == self::STATUS_COMPLETE
                    && $this->beforeupdate->get_status() != $this->get_status();
            if ($mustarchivecompetencies) {
                $competencies = $this->get_competencies();
                $usercompetencies = user_competency::get_multiple($this->get_userid(), $competencies);

                // Copy all user competencies to user_competency_plan table.
                foreach ($usercompetencies as $uckey => $uc) {
                    $ucprecord = $uc->to_record();
                    $ucprecord->planid = $this->get_id();
                    unset($ucprecord->id);

                    $usercompetencyplan = new user_competency_plan(0, $ucprecord);
                    $usercompetencyplan->create();
                }
            }
        }
        $this->beforeupdate = null;
    }

    /**
     * Whether the current user can manage the plan.
     *
     * @return bool
     */
    public function can_manage() {
        if ($this->get_status() == self::STATUS_DRAFT) {
            return self::can_manage_user_draft($this->get_userid());
        }
        return self::can_manage_user($this->get_userid());
    }

    /**
     * Whether the current user can read the plan.
     *
     * @return bool
     */
    public function can_read() {
        if ($this->get_status() == self::STATUS_DRAFT) {
            return self::can_read_user_draft($this->get_userid());
        }
        return self::can_read_user($this->get_userid());
    }

    /**
     * Get the competencies in this plan.
     *
     * @return competency[]
     */
    public function get_competencies() {
        $competencies = array();
        if ($this->get_templateid()) {
            // Get the competencies from the template.
            $competencies = template_competency::list_competencies($this->get_templateid(), true);
        } else {
            // TODO MDL-50328.
            // Get the competencies in this plan.
            // $competencies = plan_competency::list_competencies($this->get_id());
        }
        return $competencies;
    }

    /**
     * Get the context in which the plan is attached.
     *
     * @return context_user
     */
    public function get_context() {
        return context_user::instance($this->get_userid());
    }

    /**
     * Human readable status name.
     *
     * @return string
     */
    public function get_statusname() {

        $status = $this->get_status();

        switch ($status) {
            case self::STATUS_DRAFT:
                $strname = 'draft';
                break;
            case self::STATUS_ACTIVE:
                $strname = 'active';
                break;
            case self::STATUS_COMPLETE:
                $strname = 'complete';
                break;
            default:
                throw new \moodle_exception('errorplanstatus', 'tool_lp', '', $status);
                break;
        }

        return get_string('planstatus' . $strname, 'tool_lp');
    }

    /**
     * Validate the template ID.
     *
     * @param mixed $value The value.
     * @return true|lang_string
     */
    protected function validate_templateid($value) {

        // Checks that the template exists.
        if (!empty($value) && !template::record_exists($value)) {
            return new lang_string('invaliddata', 'error');
        }

        return true;
    }

    /**
     * Can the current user manage a user's plan?
     *
     * @param  int $planuserid The user to whom the plan would belong.
     * @return bool
     */
    public static function can_manage_user($planuserid) {
        global $USER;
        $context = context_user::instance($planuserid);

        $capabilities = array('tool/lp:planmanage');
        if ($context->instanceid == $USER->id) {
            $capabilities[] = 'tool/lp:planmanageown';
        }

        return has_any_capability($capabilities, $context);
    }

    /**
     * Can the current user manage a user's draft plan?
     *
     * @param  int $planuserid The user to whom the plan would belong.
     * @return bool
     */
    public static function can_manage_user_draft($planuserid) {
        global $USER;
        $context = context_user::instance($planuserid);

        $capabilities = array('tool/lp:planmanagedraft');
        if ($context->instanceid == $USER->id) {
            $capabilities[] = 'tool/lp:planmanageowndraft';
        }

        return has_any_capability($capabilities, $context);
    }

    /**
     * Can the current user view a user's plan?
     *
     * @param  int $planuserid The user to whom the plan would belong.
     * @return bool
     */
    public static function can_read_user($planuserid) {
        global $USER;
        $context = context_user::instance($planuserid);

        $capabilities = array('tool/lp:planview');
        if ($context->instanceid == $USER->id) {
            $capabilities[] = 'tool/lp:planviewown';
        }

        return has_any_capability($capabilities, $context)
            || self::can_manage_user($planuserid);
    }

    /**
     * Can the current user view a user's draft plan?
     *
     * @param  int $planuserid The user to whom the plan would belong.
     * @return bool
     */
    public static function can_read_user_draft($planuserid) {
        global $USER;
        $context = context_user::instance($planuserid);

        $capabilities = array('tool/lp:planviewdraft');
        if ($context->instanceid == $USER->id) {
            $capabilities[] = 'tool/lp:planviewowndraft';
        }

        return has_any_capability($capabilities, $context)
            || self::can_manage_user_draft($planuserid);
    }

    /**
     * Return a list of status depending on capabilities.
     *
     * @param  int $userid The user to whom the plan would belong.
     * @return array
     */
    public static function get_status_list($userid) {
        $status = array();
        if (self::can_manage_user_draft($userid)) {
            $status[self::STATUS_DRAFT] = get_string('planstatusdraft', 'tool_lp');
        }
        if (self::can_manage_user($userid)) {
            $status[self::STATUS_ACTIVE] = get_string('planstatusactive', 'tool_lp');
            $status[self::STATUS_COMPLETE] = get_string('planstatuscomplete', 'tool_lp');
        }
        return $status;
    }

}
