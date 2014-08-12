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

if ($ADMIN->fulltree) {

    // Custom Fonts url.
    $name = 'theme_cleanudem/customfontsurl';
    $title = get_string('customfontsurl', 'theme_cleanudem');
    $description = get_string('customfontsurldesc', 'theme_cleanudem');
    $default = '';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '120', '4');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Footer navigation setting.
    $name = 'theme_cleanudem/footernav';
    $title = get_string('footernav', 'theme_cleanudem');
    $description = get_string('footernavdesc', 'theme_cleanudem');
    $default = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Footnote setting.
    $name = 'theme_cleanudem/footnote';
    $title = get_string('footnote', 'theme_cleanudem');
    $description = get_string('footnotedesc', 'theme_cleanudem');
    $default = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Custom CSS file.
    $name = 'theme_cleanudem/customcss';
    $title = get_string('customcss', 'theme_cleanudem');
    $description = get_string('customcssdesc', 'theme_cleanudem');
    $default = '';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Use max width for side regions.
    $name = 'theme_cleanudem/sideregionsmaxwidth';
    $title = get_string('sideregionsmaxwidth', 'theme_cleanudem');
    $description = get_string('sideregionsmaxwidthdesc', 'theme_cleanudem');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);
}
