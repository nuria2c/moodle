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
 * Allocation form.
 *
 * @module      mod_workshop/allocation
 * @category    output
 * @author      Issam Taboubi <issam.taboubi@umontreal.ca>
 * @copyright   2017 Université de Montréal
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery',
    'core/notification',
    'tool_lp/dialogue',
    'core/str'], function ($, notification, Dialogue, str) {

    /**
     * Allocation form.
     */
    var Allocation = function () {
        var self = this;
        $('input[name="allocationview"]').on('change', self.switchView.bind(self));
        $('.allocation-see-results').on('click', self.renderAllocationResults.bind(self));
        $(".random-allocation-button a").on('click', self.renderRandomAllocation.bind(self));
        $('select[name=by], select[name=of]').on('change', function (e) {
            e.preventDefault();
            window.onbeforeunload = null;
            $(e.target).parents('form:first').submit();
        });
    };

    /**
     * Show random allocation form.
     *
     * @method showRandomAllocationForm
     * @param {Dialogue} popup Dialogue object to initialise.
     */
    Allocation.prototype.showRandomAllocationForm = function (popup) {
        var body = $(popup.getContent());
        body.find('fieldset.hidden').removeClass('hidden');
        var viewToDisplay = $('input[name="allocationview"]:checked').val();
        if (viewToDisplay == 'reviewee') {
            body.find('select[name="numper"]').val(2);
            body.find('input[name="view"]').val('reviewerof');
        } else {
            body.find('select[name="numper"]').val(1);
            body.find('input[name="view"]').val('reviewedby');
        }
        body.find('.btn-cancel').on('click', function (e) {
            e.preventDefault();
            popup.close();
        });
    };

    /**
     * Display random allocation dialogue.
     *
     * @method renderRandomAllocation
     */
    Allocation.prototype.renderRandomAllocation = function () {
        var self = this;
        str.get_strings([
            {key: 'pluginname', component: 'workshopallocation_random'}
        ]).done(function (strings) {
            var html = $('.allocator-random').html();
            new Dialogue(
                    strings[0],
                    html,
                    self.showRandomAllocationForm.bind(self),
                    self.destroyDialogue
                    );
        }).fail(notification.exception);
    };

    /**
     * Display allocation results dialogue.
     *
     * @method renderAllocationResults
     */
    Allocation.prototype.renderAllocationResults = function () {
        str.get_strings([
            {key: 'allocationresults', component: 'workshopallocation_random'}
        ]).done(function (strings) {
            var html = $('.allocation-results-container').html();
            new Dialogue(
                    strings[0],
                    html
                    );
        }).fail(notification.exception);
    };

    /**
     * Switch view.
     *
     * @method switchView
     */
    Allocation.prototype.switchView = function () {
        var viewToDisplay = $('input[name="allocationview"]:checked').val();
        if (viewToDisplay == 'reviewee') {
            $('.allocations .reviewerof').hide();
            $('.allocations .reviewedby').show();
        } else {
            $('.allocations .reviewedby').hide();
            $('.allocations .reviewerof').show();
        }
    };

    /**
     * destroy DOM after close.
     *
     * @param Dialogue
     * @method destroyDialogue
     */
    Allocation.prototype.destroyDialogue = function (dialg) {
        dialg.close();
    };

    return /** @alias module:mod_workshop/allocation */ {
        init: function () {
            // Create instance.
            new Allocation();
        }
    };
});
