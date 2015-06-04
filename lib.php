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
        $return->footnote = html_writer::div(format_text($page->theme->settings->footnote), 'footnote text-center');
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
 * @param moodle_url $url The url to analyse.
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

/**
 * Adds the JavaScript for the fullscreen mode to the page.
 *
 * @param moodle_page $page
 */
function theme_cleanudem_initialize_fullscreenmode(moodle_page $page) {
    user_preference_allow_ajax_update('theme_cleanudem_fullscreenmode_state', PARAM_ALPHA);
    $disableurl = new moodle_url($page->url, array('fullscreenmodestate' => 'true'));
    $page->requires->yui_module(
        'moodle-theme_cleanudem-fullscreenmode',
        'M.theme_cleanudem.initFullscreenMode',
        array(array('disableurl' => $disableurl))
    );
    $strings = array('enablefullscreenmode', 'disablefullscreenmode', 'fullscreenactivated');
    $page->requires->strings_for_js($strings, 'theme_cleanudem');
}

/**
 * Adds the JavaScript for front page slide show.
 *
 * @param moodle_page $page
 */
function theme_cleanudem_initialize_slideshow(moodle_page $page) {
    $page->requires->jquery();
    $page->requires->jquery_plugin('bootstrap', 'theme_cleanudem');
}

/**
 * Gets fullscreen mode state the user has selected, or the default if they have never changed.
 *
 * @param string $default The default colour to use, normally red
 * @return boolean The fullscreen mode state the user has selected
 */
function theme_cleanudem_get_fullscreenmode_state($default = 'false') {
    return to_strict_boolean(get_user_preferences('theme_cleanudem_fullscreenmode_state', $default));
}

/**
 * Check if the value entered is considered to be a true value.
 *
 * @param mixed $val The value to consider.
 * @param array $truevalues The values considered to be true.
 * @return boolean If the value is true.
 */
function to_strict_boolean($val, $truevalues = array('true')) {
    if (is_string($val)) {
        return (in_array($val, $truevalues));
    } else {
        return (boolean) $val;
    }
}

/**
 * Checks if the user is switching colours with a refresh (JS disabled).
 * If they are this updates the users preference in the database.
 *
 * @return bool if the optionnal param is setted
 */
function theme_cleanudem_check_fullscreenmode() {
    $fullscreenmodestate = optional_param('fullscreenmodestate', null, PARAM_ALPHA);
    $hasstatechanged = in_array($fullscreenmodestate, array('true', 'false'));
    if ($hasstatechanged) {
        return set_user_preference('theme_cleanudem_fullscreenmode_state', $fullscreenmodestate);
    }
    return false;
}

/**
 * Get the admin setting of the current theme.
 *
 * @param string $setting The setting name.
 * @param string $format The setting format.
 * @return boolean|string The formatted setting or false if not exist.
 */
function theme_cleanudem_get_setting($setting, $format = '') {
    $theme = theme_config::load('cleanudem');
    if (empty($theme->settings->$setting)) {
        return false;
    } else if (empty($format)) {
        return $theme->settings->$setting;
    } else if ($format === 'format_text') {
        return format_text($theme->settings->$setting);
    } else {
        return format_string($theme->settings->$setting);
    }
}

/**
 * Serves any files associated with the theme settings.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return bool
 */
function theme_cleanudem_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    if ($context->contextlevel == CONTEXT_SYSTEM && preg_match("/slide[1-9][0-9]*image/", $filearea) !== false) {
        $theme = theme_config::load('cleanudem');
        // By default, theme files must be cache-able by both browsers and proxies.
        if (!array_key_exists('cacheability', $options)) {
            $options['cacheability'] = 'public';
        }
        return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
    } else {
        send_file_not_found();
    }
}

/**
 * Simple function returning a boolean true if user has roles
 * in any context, otherwise false.
 * This is the cached and simplified version of the user_has_role_assignment() method.
 *
 * @param int $userid The id of the user.
 * @param int $roleid The role id to retrieve.
 * @return bool If the user has the specified role in any context.
 */
function theme_cleanudem_user_has_role_assignment($userid, $roleid) {
    $cache = cache::make('theme_cleanudem', 'cachedisstudent');
    if (!$cachedisstudent = $cache->get('cachedisstudent')) {
        $cachedisstudent = user_has_role_assignment($userid, $roleid);
        $cache->set('cachedisstudent', $cachedisstudent);
    }
    return $cachedisstudent;
}
