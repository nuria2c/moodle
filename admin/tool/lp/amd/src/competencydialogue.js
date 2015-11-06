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
 * Display Competency in dialogue box.
 *
 * @module     tool_lp/Competencydialogue
 * @package    tool_lp
 * @copyright  2015 Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery',
        'core/notification',
        'core/ajax',
        'core/templates',
        'core/str',
        'tool_lp/dialogue'],
       function($, notification, ajax, templates, str, Dialogue) {

    /** @var {Object} - Options for tool_lp_data_for_competency_summary service. */
    var dataOptions = {
        includerelated: false,
        includecourses: false
    };

    /**
     * Callback on dialogue display, it apply enhance on competencies dialogue.
     *
     * 
     * @param {Dialogue} dialogue
     * @method enhanceDialogue
     */
    var enhanceDialogue = function(dialogue) {
       enhance(dialogue.getContent());
    };

    /**
     * Display a dialogue box with competency data.
     *
     * @param {Number} the competency id
     * @param {Object} Options for tool_lp_data_for_competency_summary service 
     * @method showDialogue
     */
    var showDialogue = function(competencyid, options) {

        $.extend(dataOptions, options);
        var datapromise = getCompetencyDataPromise(competencyid);
        datapromise.done(function(data) {
            // Inner Html in the dialogue content.
            templates.render('tool_lp/competency_summary', data)
                .done(function(html) {
                    new Dialogue(
                        data.shortname,
                        html,
                        enhanceDialogue,
                        {modal:false}
                    );
                }).fail(notification.exception);
        }).fail(notification.exception);
    };

    /**
     * The action on the click event.
     *
     * @param {Event} event click
     * @method clickEventHandler
     */
    var clickEventHandler = function(e) {

        e.preventDefault();
        var competencyid = $(e.target).data('id');

        // Show the dialogue box.
        showDialogue(competencyid);
    };

    /**
     * Get a promise on data competency.
     *
     * @param {Number} competencyid
     * @method getCompetencyDataPromise
     */
    var getCompetencyDataPromise = function(competencyid, data) {

        // Check the data not empty.
        if (data) {
            return $.when(data);
        }

        var requests = ajax.call([
            { methodname: 'tool_lp_data_for_competency_summary',
              args: { competencyid: competencyid,
                      includerelated: dataOptions.includerelated,
                      includecourses: dataOptions.includecourses
                    }
            }
        ]);

        return requests[0].then(function(context) {
           return context;
        }).fail(notification.exception);
        
    };

    /**
     * Enhance display dialogue box for competency.
     *
     * @param {Node} container
     * @param {Object} options
     * @method enhance
     */
    var enhance = function(container, options) {
        $.extend(dataOptions, options);
        $(container).off('click', '[data-action="competency-dialogue"]', clickEventHandler);
        $(container).on('click', '[data-action="competency-dialogue"]', clickEventHandler);
    };

    return {
        enhance : enhance,
        showDialogue : showDialogue
    };
});
