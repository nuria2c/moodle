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
 * Moodle's Clean UdeM theme.
 *
 * @package   theme_cleanudem
 * @copyright 2014 Universite de Montreal
 * @author    Gilles-Philippe Leblanc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Clean UdeM maintenance renderer.
 *
 * @copyright 2014 Universite de Montreal
 * @author    Gilles-Philippe Leblanc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class theme_cleanudem_core_renderer_maintenance extends core_renderer_maintenance {

    /**
     * This renders a notification message.
     * Uses bootstrap compatible html.
     *
     * @param string $message The notification message to render.
     * @param string $classes The message type.
     * @return string the html fragment generated message.
     */
    public function notification($message, $classes = 'notifyproblem') {
        $message = clean_text($message);

        if ($classes == 'notifyproblem') {
            return html_writer::div($message, 'alert alert-danger');
        }
        if ($classes == 'notifywarning') {
            return html_writer::div($message, 'alert alert-warning');
        }
        if ($classes == 'notifysuccess') {
            return html_writer::div($message, 'alert alert-success');
        }
        if ($classes == 'notifymessage') {
            return html_writer::div($message, 'alert alert-info');
        }
        if ($classes == 'redirectmessage') {
            return html_writer::div($message, 'alert alert-block alert-info');
        }
        return html_writer::div($message, $classes);
    }

    /**
     * Gets the HTML for the studium logo box div.
     *
     * @param string $suffix The suffix used in the image (ex. white for the navigation logo box).
     * @return string html The generated html fragment of the logo box.
     */
    public function studium_logobox($suffix = '') {
        return theme_cleanudem_renderer_helper::studium_logobox($suffix);
    }

    /**
     * Add the favicon to the header of a page.
     *
     * @return string the html required to add the favicon.
     */
    public static function favicon_links() {
        return theme_cleanudem_renderer_helper::favicon_links();
    }

    /**
     * Add the favicon meta tags required to various devices to the header of a page.
     *
     * @return string the html required to add the meta tags.
     */
    public static function favicon_metas() {
        return theme_cleanudem_renderer_helper::favicon_metas();
    }
}
