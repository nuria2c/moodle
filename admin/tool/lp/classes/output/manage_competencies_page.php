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
defined('MOODLE_INTERNAL') || die();

use renderable;
use templatable;
use renderer_base;
use single_button;
use stdClass;
use moodle_url;
use context_system;
use tool_lp\api;
use tool_lp\competency;
use tool_lp\competency_framework;
use tool_lp\external\competency_framework_exporter;

/**
 * Class containing data for managecompetencies page
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manage_competencies_page implements renderable, templatable {

    /** @var \tool_lp\competency_framework $framework This competency framework. */
    protected $framework = null;

    /** @var \tool_lp\competency[] $competencies List of competencies. */
    protected $competencies = array();

    /** @var string $search Text to search for. */
    protected $search = '';

    /** @var bool $canmanage Result of permissions checks. */
    protected $canmanage = false;

    /** @var moodle_url $pluginurlbase Base url to use constructing links. */
    protected $pluginbaseurl = null;

    /** @var context $pagecontext The page context. */
    protected $pagecontext = null;

    /**
     * Construct this renderable.
     *
     * @param \tool_lp\competency_framework $framework Competency framework.
     * @param string $search Search string.
     * @param context $pagecontext The page context.
     */
    public function __construct($framework, $search, $pagecontext) {
        $this->framework = $framework;
        $this->pagecontext = $pagecontext;
        $this->search = $search;
        $addpage = new single_button(
           new moodle_url('/admin/tool/lp/editcompetencyframework.php'),
           get_string('addnewcompetency', 'tool_lp')
        );
        $this->navigation[] = $addpage;

        $this->canmanage = has_capability('tool/lp:competencymanage', $framework->get_context());
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output Renderer base.
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        $exporter = new competency_framework_exporter($this->framework);
        $data->framework = $exporter->export($output);
        $data->canmanage = $this->canmanage;
        $data->search = $this->search;
        $data->pagecontextid = $this->pagecontext->id;
        $data->pluginbaseurl = (new moodle_url('/admin/tool/lp'))->out(true);

        $rules = competency::get_available_rules();
        foreach ($rules as $type => $rule) {
            $rule->name = (string) $rule->name;
            $rule->type = $type;
        }
        $data->rulesmodules = json_encode(array_values($rules));

        return $data;
    }
}
