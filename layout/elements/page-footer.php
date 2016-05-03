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

if (!isset($vars['footernav'])) {
    $vars['footernav'] = '';
}
if (!isset($vars['footnote'])) {
    $vars['footnote'] = '';
}

?>

<footer id="page-footer">
    <?php echo $vars['footernav']; ?>
    <div class="footer-info">
        <div id="course-footer"><?php echo $OUTPUT->course_footer(); ?></div>
        <p class="helplink"><?php echo $OUTPUT->page_doc_link(); ?></p>
        <?php echo $vars['footnote']; ?>
        <?php echo $OUTPUT->login_info(); ?>
        <?php echo $OUTPUT->home_link(); ?>
        <?php echo $OUTPUT->standard_footer_html(); ?>
        <?php $a = explode('.', php_uname('n')); ?>
        <p class="serverinfo">Serveur: <?php echo $a[0]; ?></p>
    </div>
</footer>

<a href="#" aria-label="Retour vers le haut de la page" class="scroll-to-top"><i class="fa fa-angle-up"></i></a>
