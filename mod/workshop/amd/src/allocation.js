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
    'core/form-autocomplete',
    'tool_lp/dialogue',
    'core/str'], function ($, notification, autocomplete, Dialogue, str) {

    /**
     * Allocation form.
     */
    var Allocation = function () {
        var self = this;
        $('input[name="allocationview"]').on('change', self.switchView.bind(self));
        $('.allocation-see-results').on('click', self.renderAllocationResults.bind(self));
        $('.manual-allocation-see-results').on('click', self.showAffectedParticipants.bind(self));
        $(".random-allocation-button a").on('click', self.renderRandomAllocation.bind(self));
        $('select[name=by], select[name=of]').on('change', function (e) {
            e.preventDefault();
            window.onbeforeunload = null;
            $(e.target).parents('form:first').submit();
        });
        $(document).ready(function () {
            str.get_strings([
                {key: 'addreviewer', component: 'workshopallocation_manual'},
                {key: 'addreviewee', component: 'workshopallocation_manual'}
            ]).done(function (strings) {
                $('.addreviewer').each(function () {
                    autocomplete.enhance('#' + $(this).attr('id'), false, false, strings[0]);
                });
                $('.addreviewee').each(function () {
                    autocomplete.enhance('#' + $(this).attr('id'), false, false, strings[1]);
                });
            }).fail(notification.exception);
        });
    };

    /** @type {Dialogue} Reference to the random attribution dialogue. */
    Allocation.prototype.randomAllocationDialogue = null;

    /** @type {Dialogue} Reference to the allocation results dialogue. */
    Allocation.prototype.allocationResultsDialogue = null;

    /** @type {Dialogue} Reference to the affected participants dialogue. */
    Allocation.prototype.affectedParticipantsDialogue = null;

    /**
     * Show random allocation form.
     *
     * @method showRandomAllocationForm
     * @param {Dialogue} dialogue Dialogue object to initialise.
     */
    Allocation.prototype.showRandomAllocationForm = function (dialogue) {
        var self = this,
                body = $(dialogue.getContent()),
                viewToDisplay = $('input[name="allocationview"]:checked').val(),
                value = (viewToDisplay == 'reviewee' ? 1 : 2);
        body.find('fieldset.hidden').removeClass('hidden');
        body.find('select[name="numper"]').val(value);
        body.find('.btn-cancel').on('click', function (e) {
            e.preventDefault();
            self.hideDialogue(dialogue);
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
            if (!self.randomAllocationDialogue) {
                var content = $('.allocator-random');
                self.randomAllocationDialogue = new Dialogue(
                    strings[0],
                    content.html(),
                    self.showRandomAllocationForm.bind(self)
                    );
                content.remove();
            } else {
                self.showDialogue(self.randomAllocationDialogue);
            }
        }).fail(notification.exception);
    };

    /**
     * Display allocation results dialogue.
     *
     * @method renderAllocationResults
     */
    Allocation.prototype.renderAllocationResults = function () {
        var self = this;
        str.get_strings([
            {key: 'allocationresults', component: 'workshopallocation_random'}
        ]).done(function (strings) {
            if (!self.allocationResultsDialogue) {
                var content = $('.allocation-results-container');
                self.allocationResultsDialogue = new Dialogue(strings[0], content.html());
                content.remove();
            } else {
                self.showDialogue(self.allocationResultsDialogue);
            }
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
            M.util.set_user_preference('workshopallocation_manual_view', 'reviewedby');
        } else {
            $('.allocations .reviewedby').hide();
            $('.allocations .reviewerof').show();
            M.util.set_user_preference('workshopallocation_manual_view', 'reviewerof');
        }
    };

    /**
     * Show affected participants popup.
     *
     * @method showAffectedParticipants
     */
    Allocation.prototype.showAffectedParticipants = function () {
        var self = this;
        str.get_strings([
            {key: 'affectedparticipants', component: 'workshopallocation_manual'}
        ]).done(function (strings) {
            if (!self.affectedParticipantsDialogue) {
                var content = $('.allocation-popup-content');
                self.affectedParticipantsDialogue = new Dialogue(
                    strings[0],
                    content.html(),
                    function () {},
                    function () {},
                    true
                );
                content.remove();
            } else {
                self.showDialogue(self.affectedParticipantsDialogue);
            }
        }).fail(notification.exception);
    };

    /**
     * Show dialogue.
     *
     * @param {Dialogue} dialogue
     * @method showDialogue
     */
    Allocation.prototype.showDialogue = function (dialogue) {
        dialogue.yuiDialogue.show();
    };

    /**
     * Hide dialogue after close.
     *
     * @param {Dialogue} dialogue
     * @method hideDialogue
     */
    Allocation.prototype.hideDialogue = function (dialogue) {
        dialogue.yuiDialogue.hide();
    };

    return /** @alias module:mod_workshop/allocation */ {
        init: function () {
            // Create instance.
            new Allocation();
        }
    };
});
