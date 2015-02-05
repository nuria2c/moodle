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
        $logo = html_writer::empty_tag('img', array('src' => $OUTPUT->pix_url('logos/logo_' . $type . $suffix, 'theme'),
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

        // Standard favicon.
        $html = html_writer::empty_tag('link', array(
            'rel' => 'shortcut icon',
            'href' => $OUTPUT->pix_url('favicon', 'theme')
        ));

        // Ipad.
        $html .= html_writer::empty_tag('link', array(
            'rel' => 'apple-touch-icon-precomposed',
            'sizes' => '72x72',
            'href' => $OUTPUT->pix_url('apple-touch-icon/apple-touch-icon-72x72-precomposed', 'theme')
        ));

        // Iphone with retina display.
        $html .= html_writer::empty_tag('link', array(
            'rel' => 'apple-touch-icon-precomposed',
            'sizes' => '114x114',
            'href' => $OUTPUT->pix_url('apple-touch-icon/apple-touch-icon-114x114-precomposed', 'theme')
        ));

        // Ipad with retina display.
        $html .= html_writer::empty_tag('link', array(
            'rel' => 'apple-touch-icon-precomposed',
            'sizes' => '144x144',
            'href' => $OUTPUT->pix_url('apple-touch-icon/apple-touch-icon-144x144-precomposed', 'theme')
        ));

        // Apple and Android default.
        $html .= html_writer::empty_tag('link', array(
            'rel' => 'apple-touch-icon-precomposed',
            'href' => $OUTPUT->pix_url('apple-touch-icon/apple-touch-icon-precomposed', 'theme')
        ));

        // Apple default.
        $html .= html_writer::empty_tag('link', array(
            'rel' => 'apple-touch-icon',
            'href' => $OUTPUT->pix_url('apple-touch-icon/apple-touch-icon', 'theme')
        ));

        return $html;
    }

    /**
     * Add the msapplication meta tags required for the windows 8 start screen tiles to the header of a page.
     *
     * @return string The html required to add the meta tags.
     */
    public static function msapplication_metas() {
        global $OUTPUT;

        $html = html_writer::empty_tag('meta', array(
            'name' => 'application-name',
            'content' => 'StudiUM'
        ));

        $html .= html_writer::empty_tag('meta', array(
            'name' => 'msapplication-TileColor',
            'content' => '#0E79D4'
        ));

        $html .= html_writer::empty_tag('meta', array(
            'name' => 'msapplication-square70x70logo',
            'content' => $OUTPUT->pix_url('msapplication/tiny', 'theme')
        ));

        $html .= html_writer::empty_tag('meta', array(
            'name' => 'msapplication-square150x150logo',
            'content' => $OUTPUT->pix_url('msapplication/square', 'theme')
        ));

        $html .= html_writer::empty_tag('meta', array(
            'name' => 'msapplication-wide310x150logo',
            'content' => $OUTPUT->pix_url('msapplication/wide', 'theme')
        ));

        $html .= html_writer::empty_tag('meta', array(
            'name' => 'msapplication-square310x310logo',
            'content' => $OUTPUT->pix_url('msapplication/large', 'theme')
        ));

        return $html;
    }

}
