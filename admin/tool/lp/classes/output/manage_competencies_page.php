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
 * Class containing data for managecompetencyframeworks page
 *
 * @package    tool_lp
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lp\output;

use renderable;
use templatable;
use renderer_base;
use single_button;
use stdClass;
use moodle_url;
use context_system;
use tool_lp\api;

/**
 * Class containing data for managecompetencies page
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manage_competencies_page implements renderable, templatable {

    /** @var \tool_lp\competency_framework $framework This competency framework. */
    var $framework = null;

    /** @var array $competencies List of competencies. */
    var $competencies = array();

    /** @var string $search Text to search for. */
    var $search = '';

    /** @var bool $canmanage Result of permissions checks. */
    var $canmanage = false;

    /** @var moodle_url $pluginurlbase Base url to use constructing links. */
    var $pluginbaseurl = null;

    /**
     * Construct this renderable.
     * @param \tool_lp\competency_framework $framework
     */
    public function __construct($framework, $search) {
        $this->framework = $framework;
        $this->search = $search;
        $addpage = new single_button(
           new moodle_url('/admin/tool/lp/editcompetencyframework.php'),
           get_string('addnewcompetency', 'tool_lp')
        );
        $this->navigation[] = $addpage;

        $this->competencies = api::search_competencies($search, $framework->get_id());

        $context = context_system::instance();
        $this->canmanage = has_capability('tool/lp:competencymanage', $context);
    }

    /**
     * Recursively build up the tree of nodes.
     *
     * @param stdClass $parent - the exported parent node
     * @param array $all - List of all competency classes.
     */
    private function add_competency_children($parent, $all) {
        foreach ($all as $one) {
            if ($one->get_parentid() == $parent->id) {
                $parent->haschildren = true;
                $record = $one->to_record();
                $record->children = array();
                $record->haschildren = false;
                $parent->children[] = $record;
                $this->add_competency_children($record, $all);
            }
        }
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        $data->framework = $this->framework->to_record();
        $data->canmanage = $this->canmanage;
        $data->competencies = array();
        $data->search = $this->search;

        foreach ($this->competencies as $competency) {
            if ($competency->get_parentid() == 0) {
                $record = $competency->to_record();
                $record->children = array();
                $record->haschildren = false;
                $data->competencies[] = $record;
                $this->add_competency_children($record, $this->competencies);
            }
        }

        return $data;
    }
}