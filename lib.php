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
 * Parses CSS before it is cached.
 *
 * This function can make alterations and replace patterns within the CSS.
 *
 * @param string $css The CSS
 * @param theme_config $theme The theme config object.
 * @return string The parsed CSS The parsed CSS.
 */
function theme_cleanudem_process_css($css, $theme) {

    // Set custom CSS.
    if (!empty($theme->settings->customcss)) {
        $customcss = $theme->settings->customcss;
    } else {
        $customcss = null;
    }
    $css = theme_cleanudem_set_customcss($css, $customcss);

    return $css;
}

/**
 * Adds any custom CSS to the CSS before it is cached.
 *
 * @param string $css The original CSS.
 * @param string $customcss The custom CSS to add.
 * @return string The CSS which now contains our custom CSS.
 */
function theme_cleanudem_set_customcss($css, $customcss) {
    $tag = '[[setting:customcss]]';
    $replacement = $customcss;
    if (is_null($replacement)) {
        $replacement = '';
    }

    $css = str_replace($tag, $replacement, $css);

    return $css;
}

/**
 * Returns an object containing HTML for the areas affected by settings.
 *
 * @param renderer_base $output Pass in $OUTPUT.
 * @param moodle_page $page Pass in $PAGE.
 * @return stdClass An object with the following properties:
 *      - footernav HTML to use as a footernav. By default ''.
 *      - footnote HTML to use as a footnote. By default ''.
 */
function theme_cleanudem_get_html_for_settings(renderer_base $output, moodle_page $page) {
    $return = new stdClass;

    $return->footernav = '';
    if (!empty($page->theme->settings->footernav)) {
        $return->footernav = html_writer::div($page->theme->settings->footernav, 'footer-nav');
    }

    $return->footnote = '';
    if (!empty($page->theme->settings->footnote)) {
        $return->footnote = html_writer::div($page->theme->settings->footnote, 'footnote text-center');
    }

    $return->fontlinks = '';
    if (!empty($page->theme->settings->customfontsurl)) {
        $urls = explode("\n", $page->theme->settings->customfontsurl);
        foreach ($urls as $url) {
            $attributes = array('href' => $url, 'rel' => 'stylesheet', 'type' => 'text/css');
            $return->fontlinks .= html_writer::empty_tag('link', $attributes);
        }
    }

    $return->sideregionsmaxwidthclass = '';
    if (!empty($page->theme->settings->sideregionsmaxwidth)) {
        $return->sideregionsmaxwidthclass = 'side-regions-with-max-width';
    }

    return $return;
}

/**
 * Detect if the current devise is a computer.
 * We check this to add mouseover support to the main menu.
 *
 * @return bool if the actual device is a computor or not.
 */
function theme_cleanudem_is_default_device_type() {
    return core_useragent::get_device_type() == core_useragent::DEVICETYPE_DEFAULT;
}

/**
 * Generate a target attribute of an url. The target
 * should be "_blank" if the url is not on the current host.
 *
 * @param type $url The url to analyse.
 * @return string The generated target.
 */
function theme_cleanudem_get_target($url) {
    global $CFG;
    $target = '';
    $urltempo = explode('#', $url);
    $url = $urltempo[0];
    if (!empty($url) && strpos($url, $CFG->wwwroot) === false) {
        $target = '_blank';
    }
    return $target;
}
