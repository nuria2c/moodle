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
 * Learning plan webservice functions.
 *
 *
 * @package    tool_lp
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$functions = array(

    // Learning plan related functions.

    'tool_lp_create_competency_framework' => array(
        'classname'    => 'tool_lp\external',
        'methodname'   => 'create_competency_framework',
        'classpath'    => '',
        'description'  => 'Creates new competency frameworks.',
        'type'         => 'write',
        'capabilities' => 'tool/lp:competencymanage',
    ),
    'tool_lp_read_competency_framework' => array(
        'classname'    => 'tool_lp\external',
        'methodname'   => 'read_competency_framework',
        'classpath'    => '',
        'description'  => 'Load a summary of a competency framework.',
        'type'         => 'read',
        'capabilities' => 'tool/lp:competencyview',
    ),
    'tool_lp_delete_competency_framework' => array(
        'classname'    => 'tool_lp\external',
        'methodname'   => 'delete_competency_framework',
        'classpath'    => '',
        'description'  => 'Delete a competency framework.',
        'type'         => 'write',
        'capabilities' => 'tool/lp:competencymanage',
    ),
    'tool_lp_update_competency_framework' => array(
        'classname'    => 'tool_lp\external',
        'methodname'   => 'update_competency_framework',
        'classpath'    => '',
        'description'  => 'Update a competency framework.',
        'type'         => 'write',
        'capabilities' => 'tool/lp:competencymanage',
    ),
    'tool_lp_list_competency_frameworks' => array(
        'classname'    => 'tool_lp\external',
        'methodname'   => 'list_competency_frameworks',
        'classpath'    => '',
        'description'  => 'Load a list of a competency frameworks.',
        'type'         => 'read',
        'capabilities' => 'tool/lp:competencyview',
    ),
    'tool_lp_count_competency_frameworks' => array(
        'classname'    => 'tool_lp\external',
        'methodname'   => 'count_competency_frameworks',
        'classpath'    => '',
        'description'  => 'Count a list of a competency frameworks.',
        'type'         => 'read',
        'capabilities' => 'tool/lp:competencyview',
    ),
    'tool_lp_data_for_competency_frameworks_manage_page' => array(
        'classname'    => 'tool_lp\external',
        'methodname'   => 'data_for_competency_frameworks_manage_page',
        'classpath'    => '',
        'description'  => 'Load the data for the competency frameworks manage page template',
        'type'         => 'read',
        'capabilities' => 'tool/lp:competencyview',
    ),
    'tool_lp_create_competency' => array(
        'classname'    => 'tool_lp\external',
        'methodname'   => 'create_competency',
        'classpath'    => '',
        'description'  => 'Creates new competencies.',
        'type'         => 'write',
        'capabilities' => 'tool/lp:competencymanage',
    ),
    'tool_lp_read_competency' => array(
        'classname'    => 'tool_lp\external',
        'methodname'   => 'read_competency',
        'classpath'    => '',
        'description'  => 'Load a summary of a competency.',
        'type'         => 'read',
        'capabilities' => 'tool/lp:competencyview',
    ),
    'tool_lp_delete_competency' => array(
        'classname'    => 'tool_lp\external',
        'methodname'   => 'delete_competency',
        'classpath'    => '',
        'description'  => 'Delete a competency.',
        'type'         => 'write',
        'capabilities' => 'tool/lp:competencymanage',
    ),
    'tool_lp_update_competency' => array(
        'classname'    => 'tool_lp\external',
        'methodname'   => 'update_competency',
        'classpath'    => '',
        'description'  => 'Update a competency.',
        'type'         => 'write',
        'capabilities' => 'tool/lp:competencymanage',
    ),
    'tool_lp_list_competencies' => array(
        'classname'    => 'tool_lp\external',
        'methodname'   => 'list_competencies',
        'classpath'    => '',
        'description'  => 'Load a list of a competencies.',
        'type'         => 'read',
        'capabilities' => 'tool/lp:competencyview',
    ),
    'tool_lp_count_competencies' => array(
        'classname'    => 'tool_lp\external',
        'methodname'   => 'count_competencies',
        'classpath'    => '',
        'description'  => 'Count a list of a competencies.',
        'type'         => 'read',
        'capabilities' => 'tool/lp:competencyview',
    ),
    'tool_lp_search_competencies' => array(
        'classname'    => 'tool_lp\external',
        'methodname'   => 'search_competencies',
        'classpath'    => '',
        'description'  => 'Search a list of a competencies.',
        'type'         => 'read',
        'capabilities' => 'tool/lp:competencyview',
    ),
    'tool_lp_data_for_competencies_manage_page' => array(
        'classname'    => 'tool_lp\external',
        'methodname'   => 'data_for_competencies_manage_page',
        'classpath'    => '',
        'description'  => 'Load the data for the competencies manage page template',
        'type'         => 'read',
        'capabilities' => 'tool/lp:competencyview',
    ),
    'tool_lp_set_parent_competency' => array(
        'classname'    => 'tool_lp\external',
        'methodname'   => 'set_parent_competency',
        'classpath'    => '',
        'description'  => 'Set a new parent for a competency.',
        'type'         => 'write',
        'capabilities' => 'tool/lp:competencymanage',
    ),
    'tool_lp_move_up_competency' => array(
        'classname'    => 'tool_lp\external',
        'methodname'   => 'move_up_competency',
        'classpath'    => '',
        'description'  => 'Re-order a competency.',
        'type'         => 'write',
        'capabilities' => 'tool/lp:competencymanage',
    ),
    'tool_lp_move_down_competency' => array(
        'classname'    => 'tool_lp\external',
        'methodname'   => 'move_down_competency',
        'classpath'    => '',
        'description'  => 'Re-order a competency.',
        'type'         => 'write',
        'capabilities' => 'tool/lp:competencymanage',
    ),
    'tool_lp_list_competencies_in_course' => array(
        'classname'    => 'tool_lp\external',
        'methodname'   => 'list_competencies_in_course',
        'classpath'    => '',
        'description'  => 'List the competencies in a course',
        'type'         => 'read',
        'capabilities' => 'tool/lp:coursecompetencyread',
    ),
    'tool_lp_list_courses_using_competency' => array(
        'classname'    => 'tool_lp\external',
        'methodname'   => 'list_courses_using_competency',
        'classpath'    => '',
        'description'  => 'List the courses using a competency',
        'type'         => 'read',
        'capabilities' => 'tool/lp:coursecompetencyread',
    ),
    'tool_lp_count_competencies_in_course' => array(
        'classname'    => 'tool_lp\external',
        'methodname'   => 'count_competencies_in_course',
        'classpath'    => '',
        'description'  => 'List the competencies in a course',
        'type'         => 'read',
        'capabilities' => 'tool/lp:coursecompetencyread',
    ),
    'tool_lp_count_courses_using_competency' => array(
        'classname'    => 'tool_lp\external',
        'methodname'   => 'count_courses_using_competency',
        'classpath'    => '',
        'description'  => 'List the courses using a competency',
        'type'         => 'read',
        'capabilities' => 'tool/lp:coursecompetencyread',
    ),
    'tool_lp_add_competency_to_course' => array(
        'classname'    => 'tool_lp\external',
        'methodname'   => 'add_competency_to_course',
        'classpath'    => '',
        'description'  => 'Add the competency to a course',
        'type'         => 'write',
        'capabilities' => 'tool/lp:coursecompetencymanage',
    ),
    'tool_lp_add_competency_to_template' => array(
        'classname'    => 'tool_lp\external',
        'methodname'   => 'add_competency_to_template',
        'classpath'    => '',
        'description'  => 'Add the competency to a template',
        'type'         => 'write',
        'capabilities' => 'tool/lp:templatemanage',
    ),
    'tool_lp_remove_competency_from_course' => array(
        'classname'    => 'tool_lp\external',
        'methodname'   => 'remove_competency_from_course',
        'classpath'    => '',
        'description'  => 'Remove a competency from a course',
        'type'         => 'write',
        'capabilities' => 'tool/lp:coursecompetencymanage',
    ),
    'tool_lp_remove_competency_from_template' => array(
        'classname'    => 'tool_lp\external',
        'methodname'   => 'remove_competency_from_template',
        'classpath'    => '',
        'description'  => 'Remove a competency from a template',
        'type'         => 'write',
        'capabilities' => 'tool/lp:templatemanage',
    ),
    'tool_lp_data_for_course_competencies_page' => array(
        'classname'    => 'tool_lp\external',
        'methodname'   => 'data_for_course_competencies_page',
        'classpath'    => '',
        'description'  => 'Load the data for the course competencies page template.',
        'type'         => 'read',
        'capabilities' => 'tool/lp:coursecompetencyread',
    ),
    'tool_lp_data_for_template_competencies_page' => array(
        'classname'    => 'tool_lp\external',
        'methodname'   => 'data_for_template_competencies_page',
        'classpath'    => '',
        'description'  => 'Load the data for the template competencies page template.',
        'type'         => 'read',
        'capabilities' => 'tool/lp:templateread',
    ),
    'tool_lp_reorder_course_competency' => array(
        'classname'    => 'tool_lp\external',
        'methodname'   => 'reorder_course_competency',
        'classpath'    => '',
        'description'  => 'Move a course competency to a new relative sort order.',
        'type'         => 'write',
        'capabilities' => 'tool/lp:coursecompetencymanage',
    ),
    'tool_lp_reorder_template_competency' => array(
        'classname'    => 'tool_lp\external',
        'methodname'   => 'reorder_template_competency',
        'classpath'    => '',
        'description'  => 'Move a template competency to a new relative sort order.',
        'type'         => 'write',
        'capabilities' => 'tool/lp:templatemanage',
    ),
    'tool_lp_create_template' => array(
        'classname'    => 'tool_lp\external',
        'methodname'   => 'create_template',
        'classpath'    => '',
        'description'  => 'Creates new learning plan templates.',
        'type'         => 'write',
        'capabilities' => 'tool/lp:templatemanage',
    ),
    'tool_lp_read_template' => array(
        'classname'    => 'tool_lp\external',
        'methodname'   => 'read_template',
        'classpath'    => '',
        'description'  => 'Load a summary of a learning plan template.',
        'type'         => 'read',
        'capabilities' => 'tool/lp:templateview',
    ),
    'tool_lp_delete_template' => array(
        'classname'    => 'tool_lp\external',
        'methodname'   => 'delete_template',
        'classpath'    => '',
        'description'  => 'Delete a learning plan template.',
        'type'         => 'write',
        'capabilities' => 'tool/lp:templatemanage',
    ),
    'tool_lp_update_template' => array(
        'classname'    => 'tool_lp\external',
        'methodname'   => 'update_template',
        'classpath'    => '',
        'description'  => 'Update a learning plan template.',
        'type'         => 'write',
        'capabilities' => 'tool/lp:templatemanage',
    ),
    'tool_lp_list_templates' => array(
        'classname'    => 'tool_lp\external',
        'methodname'   => 'list_templates',
        'classpath'    => '',
        'description'  => 'Load a list of a learning plan templates.',
        'type'         => 'read',
        'capabilities' => 'tool/lp:templateview',
    ),
    'tool_lp_count_templates' => array(
        'classname'    => 'tool_lp\external',
        'methodname'   => 'count_templates',
        'classpath'    => '',
        'description'  => 'Count a list of a learning plan templates.',
        'type'         => 'read',
        'capabilities' => 'tool/lp:templateview',
    ),
    'tool_lp_data_for_templates_manage_page' => array(
        'classname'    => 'tool_lp\external',
        'methodname'   => 'data_for_templates_manage_page',
        'classpath'    => '',
        'description'  => 'Load the data for the learning plan templates manage page template',
        'type'         => 'read',
        'capabilities' => 'tool/lp:templateview',
    ),
    'tool_lp_create_plan' => array(
        'classname'    => 'tool_lp\external',
        'methodname'   => 'create_plan',
        'classpath'    => '',
        'description'  => 'Creates a learning plan.',
        'type'         => 'write',
        'capabilities' => 'tool/lp:planmanageall',
    ),
    'tool_lp_update_plan' => array(
        'classname'    => 'tool_lp\external',
        'methodname'   => 'update_plan',
        'classpath'    => '',
        'description'  => 'Updates a learning plan.',
        'type'         => 'write',
        'capabilities' => 'tool/lp:planmanageall',
    ),
    'tool_lp_read_plan' => array(
        'classname'    => 'tool_lp\external',
        'methodname'   => 'read_plan',
        'classpath'    => '',
        'description'  => 'Load a learning plan.',
        'type'         => 'read',
        'capabilities' => 'tool/lp:planviewown',
    ),
    'tool_lp_delete_plan' => array(
        'classname'    => 'tool_lp\external',
        'methodname'   => 'delete_plan',
        'classpath'    => '',
        'description'  => 'Delete a learning plan.',
        'type'         => 'write',
        'capabilities' => 'tool/lp:planmanageall',
    ),
    'tool_lp_data_for_plans_page' => array(
        'classname'    => 'tool_lp\external',
        'methodname'   => 'data_for_plans_page',
        'classpath'    => '',
        'description'  => 'Load the data for the plans page template',
        'type'         => 'read',
        'capabilities' => 'tool/lp:planviewown',
    ),
    'tool_lp_get_scale_values' => array(
        'classname'    => 'tool_lp\external',
        'methodname'   => 'get_scale_values',
        'classpath'    => '',
        'description'  => 'Fetch the values for a specific scale',
        'type'         => 'read',
        'capabilities' => 'tool/lp:competencymanage',
    )
);

