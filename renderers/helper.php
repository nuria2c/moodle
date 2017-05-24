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
 * Clean UdeM renderer helper.
 *
 * @copyright 2014 Universite de Montreal
 * @author    Gilles-Philippe Leblanc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class theme_cleanudem_renderer_helper {

    /**
     * @var string The main color used in the theme.
     */
    const MAIN_COLOR = '#0e79d4';

    /**
     * Gets the HTML for the studium logo box div.
     *
     * @param string $suffix The suffix used in the image (ex. white for the navigation logo box).
     * @return string html The generated html fragment of the logo box.
     */
    public static function studium_logobox($suffix = '') {
        global $CFG;
        $class = array('logobox');
        $title = '';
        if (!empty($suffix)) {
            $class[] = $suffix;
            $class[] = 'pull-left';
            $suffix  = "_$suffix";
        } else {
            $title = self::site_fullname();
        }
        $html = html_writer::start_div(implode(' ', $class));
        $html .= self::studium_logo('udem', 'http://www.umontreal.ca/', $suffix);
        $html .= html_writer::span('', 'logo_separator');
        $html .= self::studium_logo('studium', $CFG->wwwroot , $suffix);
        $html .= $title;
        $html .= html_writer::end_div();

        return $html;
    }

    /**
     * Render a studium logo.
     *
     * @param string $type The logo type, udem or studium.
     * @param moodle_url|string $url
     * @param string $suffix The suffix used in the image (ex. white for the navigation logo box).
     * @return string html The generated html fragment of the logo.
     */
    private static function studium_logo($type, $url, $suffix = '') {
        global $OUTPUT;
        $title = get_string($type, 'theme_cleanudem');
        $logo = html_writer::empty_tag('img', array('src' => $OUTPUT->image_url('logos/logo_' . $type . $suffix, 'theme'),
            'alt' => get_string('logoof', 'theme_cleanudem', $title)));
        $target = theme_cleanudem_get_target($url);
        $html = html_writer::link($url, $logo, array('title' => $title, 'target' => $target, 'class' => 'logo_' . $type));
        return $html;
    }

    /**
     * Return the title of the current site when not in production.
     *
     * @return string $fullname The fullname of the site.
     */
    private static function site_fullname() {
        global $CFG, $SITE;
        $prefix = get_string('studium', 'theme_cleanudem');
        $fullname = '';

        // If the site is not in production.
        if (!empty($CFG->udemlevel) && $CFG->udemlevel == UdeMLevel::Prod) {
            return $fullname;
        }

        // If the site name begin with the prefix.
        if (strpos($SITE->fullname, $prefix) === 0) {
            $fullname = str_replace($prefix, html_writer::tag('span', $prefix, array('class' => 'prefix')), $SITE->fullname);
        } else {
            $fullname = $SITE->fullname;
        }
        return html_writer::tag('h1', $fullname, array('id' => 'site_fullname'));
    }

    /**
     * Add the favicon to the header of a page.
     *
     * @return string The html required to add the favicon.
     */
    public static function favicon_links() {
        global $OUTPUT;

        $html = '';
        $html .= self::favicon_link('shortcut icon', $OUTPUT->image_url("favicon", 'theme'));
        $html .= self::apple_favicon_link(57);
        $html .= self::apple_favicon_link(60);
        $html .= self::apple_favicon_link(72);
        $html .= self::apple_favicon_link(114);
        $html .= self::apple_favicon_link(120);
        $html .= self::apple_favicon_link(144);
        $html .= self::apple_favicon_link(152);
        $html .= self::apple_favicon_link(180);
        $html .= self::favicon_link('mask-icon', $OUTPUT->image_url('icons/safari-pinned-tab', 'theme'),
                array('color' => self::MAIN_COLOR));

        return $html;
    }

    /**
     * Add the favicon meta tags required to various devices to the header of a page.
     *
     * @return string The html required to add the meta tags.
     */
    public static function favicon_metas() {

        $name = get_string('studium', 'theme_cleanudem');
        $html = '';
        $html .= self::favicon_meta('apple-mobile-web-app-title', $name);
        $html .= self::favicon_meta('application-name', $name);
        $html .= self::favicon_meta('msapplication-tooltip', $name);
        $html .= self::favicon_meta('msapplication-TileColor', self::MAIN_COLOR);
        $html .= self::favicon_meta('msapplication-navbutton-color', self::MAIN_COLOR);
        $html .= self::favicon_meta('msapplication-config', 'none');
        $html .= self::msapplication_favicon_meta('TileImage', '144x144');
        $html .= self::msapplication_favicon_meta('square70x70logo', '70x70');
        $html .= self::msapplication_favicon_meta('square150x150logo', '150x150');
        $html .= self::msapplication_favicon_meta('wide310x150logo', '310x150');
        $html .= self::msapplication_favicon_meta('square310x310logo', '310x310');

        return $html;
    }

    /**
     * Generate a favicon related html meta tag.
     *
     * @param string $name The name attribute of the meta tag
     * @param string $content The content attribute of the meta tag
     * @return string The html meta tag
     */
    private static function favicon_meta($name, $content) {
        return html_writer::empty_tag('meta', array(
            'name' => $name,
            'content' => $content
        )) . "\n";
    }

    /**
     * Generate a favicon related html meta tag.
     *
     * @param string $name The suffix of msapplication name attribute of the meta tag
     * @param string $formattedsize The size of the msapplication icon used in filename
     * @return string The html meta tag
     */
    private static function msapplication_favicon_meta($name, $formattedsize) {
        global $OUTPUT;
        return self::favicon_meta("msapplication-$name", $OUTPUT->image_url("icons/mstile-$formattedsize", 'theme'));
    }

    /**
     * Generate a apple-touch-icon favicon related html link tag.
     *
     * @param int $size The size of the image
     * @return string The html link tag
     */
    private static function apple_favicon_link($size) {
        global $OUTPUT;
        $name = 'apple-touch-icon';
        $formattedsize = $size . 'x' . $size;
        $href = $OUTPUT->image_url("icons/$name-$formattedsize", 'theme');
        $params = array('sizes' => $formattedsize);
        return self::favicon_link($name, $href, $params);
    }

    /**
     * Generate a favicon related html link tag.
     *
     * @param string $rel The rel attribute of the link tag
     * @param string $href The href attribute of the link tag
     * @param array $customparams Other custom attributes may be passed here
     * @return string The html link tag
     */
    private static function favicon_link($rel, $href, $customparams = array()) {
        $defaultparams = array('rel' => $rel, 'href' => $href);
        $params = array_merge($defaultparams, $customparams);
        return html_writer::empty_tag('link', $params) . "\n";
    }

}
