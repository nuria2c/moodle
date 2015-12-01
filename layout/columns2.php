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
 * 2 columns layout.
 *
 * Do not remove/add block regions (columns) from this file, instead edit config.php
 * to match the corresponding page types with another layout file.
 *
 * @package   theme_cleanudem
 * @copyright 2014 Universite de Montreal
 * @author    Gilles-Philippe Leblanc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Get the HTML for the settings bits.
$html = theme_cleanudem_get_html_for_settings($OUTPUT, $PAGE);

// Set default (LTR) layout mark-up for a two column page (side-pre-only).
$regionmain = 'span9 pull-right';
$sidepre = 'span3 desktop-first-column';
// Reset layout mark-up for RTL languages.
if (right_to_left()) {
    $regionmain = 'span9';
    $sidepre = 'span3 pull-right';
}

$bodyclasses = array();
$bodyclasses[] = 'two-column';
$bodyclasses[] = $html->sideregionsmaxwidthclass;

theme_cleanudem_check_fullscreenmode();

$isinfullscreenmode = theme_cleanudem_get_fullscreenmode_state();
$hassideprefakeblock = $PAGE->blocks->region_has_fakeblock('side-pre');

// Put the fullscreenmode unless we have fakeblock (read: important, unhideable block).
if ($isinfullscreenmode) {
    $bodyclasses[] = 'cleanudem-collapsed';
}
if ($hassideprefakeblock) {
    $bodyclasses[] = 'side-pre-fakeblock';
}
theme_cleanudem_initialize_fullscreenmode($PAGE);

?>

<?php echo $OUTPUT->element('head', array('additionalclasses' => $bodyclasses, 'fontlinks' => $html->fontlinks)); ?>

<?php echo $OUTPUT->element('header', array('fullscreenbutton' => $OUTPUT->fullscreen_button($isinfullscreenmode))); ?>

<div id="page" class="container-fluid">

    <?php echo $OUTPUT->element('page-header'); ?>

    <div id="page-content" class="row-fluid">

        <section id="region-main" class="<?php echo $regionmain; ?>">
            <?php
            echo $OUTPUT->course_content_header();
            echo $OUTPUT->main_content();
            echo $OUTPUT->course_content_footer();
            ?>
        </section>

<?php echo $OUTPUT->blocks('side-pre', $sidepre); ?>
    </div>

    <?php echo $OUTPUT->element('page-footer', array('footernav' => $html->footernav, 'footnote' => $html->footnote)); ?>

</div>

<?php echo $OUTPUT->element('foot');
