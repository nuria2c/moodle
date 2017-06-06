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
 * Renderer class for the manual allocation UI is defined here
 *
 * @package    workshopallocation
 * @subpackage manual
 * @copyright  2009 David Mudrak <david.mudrak@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/form/autocomplete.php');
/**
 * Manual allocation renderer class
 */
class workshopallocation_manual_renderer extends mod_workshop_renderer  {

    /** @var workshop module instance */
    protected $workshop;

    /** @var string allocation view */
    protected $view;

    ////////////////////////////////////////////////////////////////////////////
    // External rendering API
    ////////////////////////////////////////////////////////////////////////////

    /**
     * Display the table of all current allocations and widgets to modify them
     *
     * @param workshopallocation_manual_allocations $data to be displayed
     * @return string html code
     */
    protected function render_workshopallocation_manual_allocations(workshopallocation_manual_allocations $data) {
        global $PAGE;
        $output     = $PAGE->get_renderer('workshopallocation_manual');
        $this->workshop     = $data->workshop;
        $this->view         = get_user_preferences('workshopallocation_manual_view', 'reviewedby');

        $allocations        = $data->allocations;       // array prepared array of all allocations data
        $userinfo           = $data->userinfo;          // names and pictures of all required users
        $authors            = $data->authors;           // array potential reviewees
        $reviewers          = $data->reviewers;         // array potential submission reviewers
        $hlauthorid         = $data->hlauthorid;        // int id of the author to highlight
        $hlreviewerid       = $data->hlreviewerid;      // int id of the reviewer to highlight
        $selfassessment     = $data->selfassessment;    // bool is the self-assessment allowed in this workshop?

        if (empty($allocations)) {
            return '';
        }

        // convert user collections into drop down menus
        $authors    = array_map('fullname', $authors);
        $reviewers  =  array_map('fullname', $reviewers);
        $classtableviewselected = ($this->view == 'reviewedby') ? 'reviewee' : 'reviewer';

        $table              = new html_table();
        $table->attributes['class'] = 'allocations' . ' ' . $classtableviewselected;
        $table->head        = array(get_string('participant', 'workshop'),
                                    get_string('participantreviewedby', 'workshop'),
                                    get_string('participantrevierof', 'workshop'));
        $table->rowclasses  = array();
        $table->colclasses  = array('peer', 'reviewedby', 'reviewerof');
        $table->data        = array();
        foreach ($allocations as $allocation) {
            $row = array();
            $row[] = $this->helper_participant($allocation, $userinfo);
            $row[] = $this->helper_reviewers_of_participant($allocation, $userinfo, $reviewers, $selfassessment);
            $row[] = $this->helper_reviewees_of_participant($allocation, $userinfo, $authors, $selfassessment);
            $thisrowclasses = array();
            if ($allocation->userid == $hlauthorid) {
                $thisrowclasses[] = 'highlightreviewedby';
            }
            if ($allocation->userid == $hlreviewerid) {
                $thisrowclasses[] = 'highlightreviewerof';
            }
            $table->rowclasses[] = implode(' ', $thisrowclasses);
            $table->data[] = $row;
        }
        // Allocation header.
        $header = html_writer::start_tag('fieldset');
        $header .= html_writer::start_tag('legend');
        $header .= get_string(\mod_workshop\wizard\peerallocation_step::NAME, 'workshop');
        $header .= html_writer::end_tag('legend');
        if ($this->workshop->assessmenttype != \workshop::SELF_ASSESSMENT) {
            $header .= $this->helper_header_allocation();
        }

        $html = $header . html_writer::table($table);
        $html .= html_writer::end_tag('fieldset');

        return $this->output->container($html, 'manual-allocator');
    }

    /**
     * Get Html header allocation (view switcher and random allocation button).
     *
     * @return string Html header allocation
     */
    protected function helper_header_allocation() {
        $header = html_writer::start_tag('div', array('class' => 'header-allocation'));
        $header .= html_writer::start_tag('div', array('class' => 'allocation-view-switcher'));
        $label = get_string('allocateaccordingto', 'workshop');
        $header .= html_writer::label($label, 'allocation-view-switcher');

        $checkreviewedby = ($this->view == 'reviewedby') ? 'checked' : null;
        $checkreviewerof = ($this->view == 'reviewerof') ? 'checked' : null;
        $radioattributes = array(
            'class' => 'radio-view-switcher',
            'id' => 'id_allocationreviewer',
            'type' => 'radio',
            'name' => 'allocationview',
            'checked' => $checkreviewedby,
            'value' => 'reviewee'
        );
        $radioreviewedby = html_writer::empty_tag('input', $radioattributes);
        $radioreviewedby .= html_writer::label(get_string('reviewer', 'workshopallocation_manual'), 'id_allocationreviewer');
        $radioreviewedby = html_writer::span($radioreviewedby);

        $radioattributes = array(
            'class' => 'radio-view-switcher',
            'id' => 'id_allocationreviewee',
            'type' => 'radio',
            'name' => 'allocationview',
            'checked' => $checkreviewerof,
            'value' => 'reviewer'
        );
        $radioreviewerof = html_writer::empty_tag('input', $radioattributes);
        $radioreviewerof .= html_writer::label(get_string('reviewee', 'workshopallocation_manual'), 'id_allocationreviewee');
        $radioreviewerof = html_writer::span($radioreviewerof);

        $header .= $radioreviewedby . $radioreviewerof;

        $header .= html_writer::end_tag('div');
        $header .= html_writer::start_tag('div', array('class' => 'random-allocation-button'));
        $header .= html_writer::link('#',
            get_string('pluginname', 'workshopallocation_random'), array('class' => 'btn btn-default'));
        $header .= html_writer::end_tag('div');
        $header .= html_writer::end_tag('div');
        return $header;
    }

    ////////////////////////////////////////////////////////////////////////////
    // Internal helper methods
    ////////////////////////////////////////////////////////////////////////////

    /**
     * Returns information about the workshop participant
     *
     * @return string HTML code
     */
    protected function helper_participant(stdclass $allocation, array $userinfo) {
        $o  = $this->output->user_picture($userinfo[$allocation->userid], array('courseid' => $this->page->course->id));
        $o .= fullname($userinfo[$allocation->userid]);
        $o .= $this->output->container_start(array('submission'));
        if (is_null($allocation->submissionid)) {
            $o .= $this->output->container(get_string('nosubmissionfound', 'workshop'), 'info');
        } else {
            $link = $this->workshop->submission_url($allocation->submissionid);
            $o .= $this->output->container(html_writer::link($link, format_string($allocation->submissiontitle)), 'title');
            if (is_null($allocation->submissiongrade)) {
                $o .= $this->output->container(get_string('nogradeyet', 'workshop'), array('grade', 'missing'));
            } else {
                $o .= $this->output->container(get_string('alreadygraded', 'workshop'), array('grade', 'missing'));
            }
        }
        $o .= $this->output->container_end();
        return $o;
    }

    /**
     * Returns information about the current reviewers of the given participant and a selector do add new one
     *
     * @return string html code
     */
    protected function helper_reviewers_of_participant(stdclass $allocation, array $userinfo, array $reviewers, $selfassessment) {
        global $PAGE;
        $o = '';
        $o .= html_writer::start_tag('ul', array());
        foreach ($allocation->reviewedby as $reviewerid => $assessmentid) {
            $o .= html_writer::start_tag('li', array());
            $o .= html_writer::start_tag('span',
                array('role' => 'listitem', 'aria-selected' => true, 'class' => "label label-info"));

            // Delete icon.
            if ($reviewerid != $allocation->userid) {
                $handler = new moodle_url($this->page->url, array('mode' => 'del', 'what' => $assessmentid,
                        'sesskey' => sesskey(), 'view' => 'reviewedby'));
                $link = $this->helper_remove_allocation_icon($handler);
                $o .= html_writer::span($link, 'delete-user-allocation', array('aria-hidden' => "true"));
            }
            $o .= html_writer::start_tag('span');
            $o .= $this->output->user_picture($userinfo[$reviewerid],
                array('courseid' => $this->page->course->id, 'size' => 16));
            $o .= html_writer::end_tag('span');
            $o .= html_writer::start_tag('span');
            if ($reviewerid == $allocation->userid) {
                $o .= get_string('selfassessment', 'workshop');
            } else {
                $o .= fullname($userinfo[$reviewerid]);
            }
            $o .= html_writer::end_tag('span');
            $o .= html_writer::end_tag('span');
            $o .= html_writer::end_tag('li');
        }
        $o .= html_writer::end_tag('ul');
        
        // Build a list of possible reviewers.
        if ($this->workshop->assessmenttype != workshop::SELF_ASSESSMENT) {
            $exclude = array();
            if ($this->workshop->assessmenttype == workshop::PEER_ASSESSMENT) {
                $exclude[$allocation->userid] = true;
            }
            foreach ($allocation->reviewedby as $reviewerid => $assessmentid) {
                $exclude[$reviewerid] = true;
            }
            $options = array_diff_key($reviewers, $exclude);
            if ($options) {
                $options = array('' => '') + $options;
                $handler = new moodle_url($this->page->url,
                        array('mode' => 'new', 'of' => $allocation->userid, 'sesskey' => sesskey(), 'view' => 'reviewedby'));
                $select = new single_select($handler, 'by', $options, '', array(), 'addreviewof' . $allocation->userid);
                $select->attributes['id'] = uniqid();
                $PAGE->requires->js_call_amd('core/form-autocomplete',
                    'enhance',
                    $params = array('#' . $select->attributes['id'],
                        false,
                        false,
                        get_string('addreviewer', 'workshopallocation_manual')
                        )
                    );
                $o .= $this->output->render($select);
            }
        }

        return $o;
    }

    /**
     * Returns information about the current reviewees of the given participant and a selector do add new one
     *
     * @return string html code
     */
    protected function helper_reviewees_of_participant(stdclass $allocation, array $userinfo, array $authors, $selfassessment) {
        global $PAGE;
        $o = '';
        $o .= html_writer::start_tag('ul', array());
        foreach ($allocation->reviewerof as $authorid => $assessmentid) {
            $o .= html_writer::start_tag('li', array());
            $o .= html_writer::start_tag('span',
                array('role' => 'listitem', 'aria-selected' => true, 'class' => "label label-info"));

            // Delete icon.
            if ($authorid != $allocation->userid) {
                $handler = new moodle_url($this->page->url, array('mode' => 'del', 'what' => $assessmentid,
                        'sesskey' => sesskey(), 'view' => 'reviewedby'));
                $link = $this->helper_remove_allocation_icon($handler);
                $o .= html_writer::span($link, 'delete-user-allocation', array('aria-hidden' => "true"));
            }
            $o .= html_writer::start_tag('span');
            $o .= $this->output->user_picture($userinfo[$authorid], array('courseid' => $this->page->course->id, 'size' => 16));
            $o .= html_writer::end_tag('span');
            $o .= html_writer::start_tag('span');
             if ($authorid == $allocation->userid) {
                $o .= get_string('selfassessment', 'workshop');
            } else {
                $o .= fullname($userinfo[$authorid]);
            }
            $o .= html_writer::end_tag('span');
            $o .= html_writer::end_tag('span');
            $o .= html_writer::end_tag('li');
        }
        $o .= html_writer::end_tag('ul');

        // Build a list of possible reviewees.
        if ($this->workshop->assessmenttype != workshop::SELF_ASSESSMENT) {
            $exclude = array();
            if ($this->workshop->assessmenttype == workshop::PEER_ASSESSMENT) {
                $exclude[$allocation->userid] = true;
            }
            foreach ($allocation->reviewerof as $authorid => $assessmentid) {
                $exclude[$authorid] = true;
            }
            $options = array_diff_key($authors, $exclude);
            if ($options) {
                $options = array('' => '') + $options;
                $handler = new moodle_url($this->page->url,
                    array('mode' => 'new', 'by' => $allocation->userid, 'sesskey' => sesskey(), 'view' => 'reviewerof'));
                $select = new single_select($handler,
                    'of', $options, '', array('' => get_string('chooseuser', 'workshop')), 'addreviewby' . $allocation->userid);
                $select->attributes['id'] = uniqid();
                $PAGE->requires->js_call_amd('core/form-autocomplete',
                        'enhance',
                        $params = array('#' . $select->attributes['id'],
                            false,
                            false,
                            get_string('addreviewee', 'workshopallocation_manual')
                            )
                        );
                $o .= $this->output->render($select);
            }
        }
        return $o;
    }

    /**
     * Generates an icon link to remove the allocation
     *
     * @param moodle_url $link to the action
     * @return html code to be displayed
     */
    protected function helper_remove_allocation_icon($link) {
        return $this->output->action_link($link, 'X');
    }
}
