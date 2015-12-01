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
 * Header element.
 *
 * @package   theme_cleanudem
 * @copyright 2014 Universite de Montreal
 * @author    Gilles-Philippe Leblanc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if (!isset($vars['button'])) {
    $vars['button'] = $OUTPUT->page_heading_button();
}
if (!isset($vars['heading'])) {
    $vars['heading'] = $OUTPUT->context_header();
}

?>

<header id="page-header" class="clearfix">
    <div id="page-navbar" class="clearfix">
        <nav class="breadcrumb-nav"><?php echo $OUTPUT->navbar(); ?></nav>
        <div class="breadcrumb-button"><?php echo $vars['button']; ?></div>
    </div>
    <?php echo $vars['heading']; ?>
    <div id="course-header">
        <?php echo $OUTPUT->course_header(); ?>
    </div>
</header>
