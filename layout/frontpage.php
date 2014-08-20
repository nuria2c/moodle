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
 * frontage layout layout.
 *
 * @package   theme_cleanudem
 * @copyright 2014 Universite de Montreal
 * @author    Gilles-Philippe Leblanc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Get the HTML for the settings bits.
$html = theme_cleanudem_get_html_for_settings($OUTPUT, $PAGE);
$isediting = $PAGE->user_is_editing();

$left = (!right_to_left());  // To know if to add 'pull-right' and 'desktop-first-column' classes in the layout for LTR.

$bodyclasses = array();
$regionmainclasses = array('span12');
$sidepreblocks = '';
$fullscreenbutton = '';
if ($isediting) {
    $bodyclasses[] = 'two-column';
    $bodyclasses[] = $html->sideregionsmaxwidthclass;
    $regionmainclasses[] = 'span9';
    $sidepreregionclasses = array('span3');
    if ($left) {
        $regionmainclasses[] = 'pull-right';
        $sidepreregionclasses[] = 'desktop-first-column';
    }
    $sidepreblocks = $OUTPUT->blocks('side-pre', implode(' ', $sidepreregionclasses));

    theme_cleanudem_check_fullscreenmode();

    $isinfullscreenmode = theme_cleanudem_get_fullscreenmode_state();
    $fullscreenbutton = $OUTPUT->fullscreen_button($isinfullscreenmode);

    // Put the fullscreenmode if necessary.
    if ($isinfullscreenmode) {
        $bodyclasses[] = 'cleanudem-collapsed';
    }
    theme_cleanudem_initialize_fullscreenmode($PAGE);
}
?>

<?php echo $OUTPUT->element('head', array('additionalclasses' => $bodyclasses, 'fontlinks' => $html->fontlinks)); ?>

<?php echo $OUTPUT->element('header', array('brand' => '', 'fullscreenbutton' => $fullscreenbutton)); ?>

<div id="page" class="container-fluid">

    <?php $vars = array('heading' => $OUTPUT->studium_logobox(), 'button' => $OUTPUT->frontpage_heading_button()); ?>
    <?php echo $OUTPUT->element('page-header', $vars); ?>

    <div id="page-content" class="row-fluid">
        <section id="region-main" class="<?php echo implode(' ', $regionmainclasses); ?>">
            <?php
            echo $OUTPUT->course_content_header();
            echo $OUTPUT->main_content();
            echo $OUTPUT->course_content_footer();
            ?>
        </section>
        <?php echo $sidepreblocks; ?>
    </div>

    <?php echo $OUTPUT->element('page-footer', array('footernav' => $html->footernav, 'footnote' => $html->footnote)); ?>

</div>

<?php echo $OUTPUT->element('foot');
