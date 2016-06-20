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

defined('MOODLE_INTERNAL') || die();

// Get the HTML for the settings bits.
$html = theme_cleanudem_get_html_for_settings($OUTPUT, $PAGE);

echo $OUTPUT->doctype(); ?>
<html <?php echo $OUTPUT->htmlattributes(); ?>>
<head>
    <title><?php echo $OUTPUT->page_title(); ?></title>
    <?php echo $OUTPUT->favicon_links(); ?>
    <?php echo $OUTPUT->standard_head_html() ?>
    <?php echo $html->fontlinks ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo $OUTPUT->favicon_metas(); ?>
</head>

<body <?php echo $OUTPUT->body_attributes(); ?>>
<?php echo $OUTPUT->standard_top_of_body_html(); ?>

<div id="page" class="container-fluid">

    <header id="page-header" class="clearfix">
        <?php echo $OUTPUT->studium_logobox(); ?>
    </header>

    <div id="page-content" class="row-fluid">
        <section id="region-main" class="span12">
            <?php echo $OUTPUT->main_content(); ?>
        </section>
    </div>

    <footer id="page-footer">
        <?php echo $OUTPUT->standard_footer_html(); ?>
        <?php $a = explode('.', php_uname('n')); ?>
        <p class="serverinfo">Serveur: <?php echo $a[0]; ?></p>
    </footer>

    <?php echo $OUTPUT->standard_end_of_body_html() ?>

</div>
</body>
</html>
