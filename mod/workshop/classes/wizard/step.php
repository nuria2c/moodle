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
 * This file defines the base class for a wizard step.
 *
 * @package    mod_workshop
 * @author     Gilles-Philippe Leblanc <gilles-philippe.leblanc@umontreal.ca>
 * @copyright  2017 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_workshop\wizard;

defined('MOODLE_INTERNAL') || die();

/**
 * The abstract wizard step class
 *
 * @author Gilles-Philippe Leblanc <gilles-philippe.leblanc@umontreal.ca>
 * @copyright  2017 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class step {

    /** @var moodleform The form for the current step */
    protected $form;

    /** @var workshop The workshop object */
    protected $workshop;

    /**
     * The constructor of the class.
     *
     * @param workshop $workshop The workshop object
     * @param string $step The current step of the wizard
     * @throws coding_exception A coding exception if the child class does not define NAME const
     */
    public function __construct($workshop, $step) {
        if (!defined('static::NAME')) {
            throw new \coding_exception('Constant NAME is not defined on subclass ' . get_class($this));
        }
        $classname = $workshop->get_validated_wizard_class_name($step . '_step_form', 'step_form');
        $this->workshop = $workshop;
        $this->form = new $classname($workshop, $this);
        $this->form->set_data($workshop->record);
    }

    /**
     * Get the current wizard step form.
     *
     * @return moodleform return the current wizard step form
     */
    public function get_form() {
        return $this->form;
    }

    /**
     * Saves the grading form elements.
     *
     * @param \stdclass $data Raw data as returned by the form editor
     * @return void
     */
    abstract public function save_form(\stdclass $data);

    /**
     * Get the previous url of the wizard page.
     *
     * @return \moodle_url The previous url of the wizard page
     */
    abstract public function get_previous_url();

    /**
     * Get the next url of the wizard page.
     *
     * @return \moodle_url The next url of the wizard page
     */
    abstract public function get_next_url();

}
