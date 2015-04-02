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

defined('MOODLE_INTERNAL') || die;

// The maximum number of slide available in the slideshow.
if (!defined('MAX_SLIDE')) {
    define('MAX_SLIDE', 8);
}

// The default number of slide available in the slideshow.
if (!defined('DEFAULT_SLIDE')) {
    define('DEFAULT_SLIDE', 3);
}

// The default slide interval in the slideshow.
if (!defined('SLIDE_INTERVAL')) {
    define('SLIDE_INTERVAL', '5000');
}

$settings = null;

if (is_siteadmin()) {

    $ADMIN->add('themes', new admin_category('theme_cleanudem', get_string('pluginname', 'theme_cleanudem')));

    // General settings header.
    $name = 'theme_cleanudem_generalheading';
    $title = get_string('generalsettings', 'admin');
    $pagesettings = new admin_settingpage($name, $title);

    // Custom Fonts url.
    $name = 'theme_cleanudem/customfontsurl';
    $title = get_string('customfontsurl', 'theme_cleanudem');
    $description = get_string('customfontsurldesc', 'theme_cleanudem');
    $default = '';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '120', '4');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $pagesettings->add($setting);

    // Footer navigation setting.
    $name = 'theme_cleanudem/footernav';
    $title = get_string('footernav', 'theme_cleanudem');
    $description = get_string('footernavdesc', 'theme_cleanudem');
    $default = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $pagesettings->add($setting);

    // Footnote setting.
    $name = 'theme_cleanudem/footnote';
    $title = get_string('footnote', 'theme_cleanudem');
    $description = get_string('footnotedesc', 'theme_cleanudem');
    $default = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $pagesettings->add($setting);

    // Custom CSS file.
    $name = 'theme_cleanudem/customcss';
    $title = get_string('customcss', 'theme_cleanudem');
    $description = get_string('customcssdesc', 'theme_cleanudem');
    $default = '';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $pagesettings->add($setting);

    // User logged in help menu.
    $name = 'theme_cleanudem/loggedhelpmenuitems';
    $title = new lang_string('loggedhelpmenuitems', 'theme_cleanudem');
    $description = new lang_string('loggedhelpmenuitems_desc', 'theme_cleanudem');
    $default = '';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_TEXT, '50', '10');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $pagesettings->add($setting);

    // Use max width for side regions.
    $name = 'theme_cleanudem/sideregionsmaxwidth';
    $title = get_string('sideregionsmaxwidth', 'theme_cleanudem');
    $description = get_string('sideregionsmaxwidthdesc', 'theme_cleanudem');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $pagesettings->add($setting);

    $ADMIN->add('theme_cleanudem', $pagesettings);

    // Slideshow settings header.
    $name = 'theme_cleanudem_slideshow';
    $title = get_string('slideshow', 'theme_cleanudem');
    $pagesettings = new admin_settingpage($name, $title);

    // Slideshow Widget Settings.
    $name = 'theme_cleanudem/slideshowheading';
    $title = get_string('slideshowheading', 'theme_cleanudem');
    $description = format_text(get_string('slideshowheadingdesc', 'theme_cleanudem', MAX_SLIDE), FORMAT_MARKDOWN);
    $pagesettings->add(new admin_setting_heading($name, $title, $description));

    // Display Slideshow.
    $name = 'theme_cleanudem/displayslideshow';
    $title = get_string('displayslideshow', 'theme_cleanudem');
    $description = get_string('displayslideshowdesc', 'theme_cleanudem');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $pagesettings->add($setting);

    // Number of slides.
    $name = 'theme_cleanudem/numberofslides';
    $title = get_string('numberofslides', 'theme_cleanudem');
    $description = get_string('numberofslidesdesc', 'theme_cleanudem');
    $choices = array();
    for ($i = 1; $i <= MAX_SLIDE; $i++) {
        $choices[$i] = (string) $i;
    }
    $pagesettings->add(new admin_setting_configselect($name, $title, $description, DEFAULT_SLIDE, $choices));

    // Slide interval.
    $name = 'theme_cleanudem/slideinterval';
    $title = get_string('slideinterval', 'theme_cleanudem');
    $description = get_string('slideintervaldesc', 'theme_cleanudem');
    $setting = new admin_setting_configtext($name, $title, $description, SLIDE_INTERVAL);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $pagesettings->add($setting);

    $numberofslides = get_config('theme_cleanudem', 'numberofslides');
    for ($i = 1; $i <= $numberofslides; $i++) {
        // This is the descriptor for slide one.
        $name = 'theme_cleanudem/slide' . $i . 'info';
        $heading = get_string('slideno', 'theme_cleanudem', array('slide' => $i));
        $information = get_string('slidenodesc', 'theme_cleanudem', array('slide' => $i));
        $setting = new admin_setting_heading($name, $heading, $information);
        $pagesettings->add($setting);

        // Title.
        $name = 'theme_cleanudem/slide' . $i;
        $title = get_string('slidetitle', 'theme_cleanudem');
        $description = get_string('slidetitledesc', 'theme_cleanudem');
        $default = '';
        $setting = new admin_setting_configtext($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $pagesettings->add($setting);

        // Image.
        $name = 'theme_cleanudem/slide' . $i . 'image';
        $title = get_string('slideimage', 'theme_cleanudem');
        $description = get_string('slideimagedesc', 'theme_cleanudem');
        $setting = new admin_setting_configstoredfile($name, $title, $description, 'slide' . $i . 'image');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $pagesettings->add($setting);

        // Use play button as link.
        $name = 'theme_cleanudem/slideuseplaybuttonaslink' . $i;
        $title = get_string('useplaybuttonaslink', 'theme_cleanudem');
        $description = get_string('useplaybuttonaslinkdesc', 'theme_cleanudem');
        $setting = new admin_setting_configcheckbox($name, $title, $description, '1');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $pagesettings->add($setting);

        // URL.
        $name = 'theme_cleanudem/slide' . $i . 'url';
        $title = get_string('slideurl', 'theme_cleanudem');
        $description = get_string('slideurldesc', 'theme_cleanudem');
        $default = '';
        $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $pagesettings->add($setting);

        // URL target.
        $name = 'theme_cleanudem/slide' . $i . 'target';
        $title = get_string('slideurltarget', 'theme_cleanudem');
        $description = get_string('slideurltargetdesc', 'theme_cleanudem');
        $target1 = get_string('slideurltargetself', 'theme_cleanudem');
        $target2 = get_string('slideurltargetnew', 'theme_cleanudem');
        $default = '_blank';
        $choices = array('_self' => $target1, '_blank' => $target2);
        $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $pagesettings->add($setting);
    }
    $ADMIN->add('theme_cleanudem', $pagesettings);
}
