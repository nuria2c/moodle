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
 * Clean UdeM core renderer.
 *
 * @copyright 2014 Universite de Montreal
 * @author    Gilles-Philippe Leblanc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class theme_cleanudem_core_renderer extends theme_bootstrapbase_core_renderer {

    /**
     * The name of the variable that differentiates the courses that are hidden compared to other.
     */
    const HIDDEN_COURSE = 'hiddencourse';

    /**
     * The key word and the name of the class divider for adding a divider in the menu.
     */
    const DIVIDER = 'divider';

    /**
     * Either returns the parent version of the header bar, or a version with the logo replacing the header.
     *
     * @since Moodle 2.9
     * @param array $headerinfo An array of header information, dependant on what type of header is being displayed. The following
     *                          array example is user specific.
     *                          heading => Override the page heading.
     *                          user => User object.
     *                          usercontext => user context.
     * @param int $headinglevel What level the 'h' tag will be.
     * @return string HTML for the header bar.
     */
    public function context_header($headerinfo = null, $headinglevel = 1) {
        global $COURSE, $USER;
        $iscoursehomepage = strpos($this->page->pagetype, 'course') === 0;
        if ($iscoursehomepage && empty($COURSE->visible)) {
            $heading = $this->page->heading;
            if (!isset($headerinfo)) {
                $headerinfo = array();
            } else if (isset($headerinfo['heading'])) {
                $heading = $headerinfo['heading'];
            }
            $headerinfo['heading'] = udem_add_unavailable_course_suffix($heading, true, $COURSE->id, $USER->id);
        }

        return parent::context_header($headerinfo, $headinglevel);
    }

    /**
     * Gets the HTML for the frontpage heading button.
     *
     * @since 2.5.1 2.6
     * @return string HTML.
     */
    public function frontpage_heading_button() {
        global $OUTPUT, $SITE;
        if ($this->page->user_allowed_editing()) {
            return $OUTPUT->edit_button(new moodle_url('course/view.php', array('id' => $SITE->id)));
        }
        return $this->page->button;
    }

    /**
     * Gets the HTML for the studium logo box div.
     *
     * @param string $suffix The suffix used in the image (ex. white for the navigation logo box).
     * @return string html The generated html fragment of the logo box.
     */
    public function studium_logobox($suffix = '') {
        return theme_cleanudem_renderer_helper::studium_logobox($suffix);
    }

    /**
     * Add the favicon to the header of a page.
     *
     * @return string the html required to add the favicon.
     */
    public static function favicon_links() {
        return theme_cleanudem_renderer_helper::favicon_links();
    }

    /**
     * Add the msapplication meta tags required for the windows 8 start screen tiles to the header of a page.
     *
     * @return string the html required to add the meta tags.
     */
    public static function msapplication_metas() {
        return theme_cleanudem_renderer_helper::msapplication_metas();
    }

    /**
     * Override the JS require function to hide a block.
     * This is required to call a custom YUI3 module.
     *
     * @param block_contents $bc A block_contents object
     */
    protected function init_block_hider_js(block_contents $bc) {
        if (!empty($bc->attributes['id']) and $bc->collapsible != block_contents::NOT_HIDEABLE) {
            $config = new stdClass;
            $config->id = $bc->attributes['id'];
            $config->title = strip_tags($bc->title);
            $config->preference = 'block' . $bc->blockinstanceid . 'hidden';
            $config->tooltipVisible = get_string('hideblocka', 'access', $config->title);
            $config->tooltipHidden = get_string('showblocka', 'access', $config->title);

            $this->page->requires->yui_module(
                'moodle-theme_cleanudem-blockhider',
                'M.theme_cleanudem.init_block_hider',
                array($config)
            );

            user_preference_allow_ajax_update($config->preference, PARAM_BOOL);
        }
    }

    /**
     * Output the custom menu.
     * Add the javascript who control the behavior of an item who have a dropdown menu.
     * Add logged in help menu items.
     *
     * @param string $custommenuitems The menu items definition in syntax required by {@link convert_text_to_menu_nodes()}
     * @return string the rendered custom menu.
     */
    public function custom_menu($custommenuitems = '') {
        global $CFG;
        $this->page->requires->yui_module(
            'moodle-theme_cleanudem-navdropdownbehavior',
            'M.theme_cleanudem.init_nav_dropdown_behavior'
        );

        if (empty($custommenuitems) && !empty($CFG->custommenuitems)) {
            $custommenuitems = $CFG->custommenuitems;
        }

        $custommenu = new custom_menu($custommenuitems, current_language());

        // Add logged in help menu items.
        $loggedhelpmenuitems = $this->page->theme->settings->loggedhelpmenuitems;
        if (!empty($loggedhelpmenuitems) && isloggedin() && !isguestuser()) {
            $helpmenu = new custom_menu($loggedhelpmenuitems, current_language());
            self::add_help_menu_items($custommenu, $helpmenu);
        }

        return $this->render_custom_menu($custommenu);
    }

    /**
     * Add items to an help menu.
     *
     * @param custom_menu $menu The menu containing the help menu item.
     * @param custom_menu $helpmenu The help menu who contains help items to add.
     */
    private static function add_help_menu_items(custom_menu $menu, custom_menu $helpmenu) {

        // Do nothing if there is no help menu items.
        if (!$helpmenu->has_children()) {
            return;
        }

        // Find "Help" menu and add it if not exists.
        $helpstring = get_string('help');
        foreach ($menu->get_children() as $child) {
            if ($child->get_text() == $helpstring) {
                $help = $child;
                break;
            }
        }

        // If the help menu is not defined in the standard custom menu, create one.
        if (empty($help)) {
            $help = $menu->add($helpstring, new moodle_url('#'), $helpstring, 0);
        }

        // Add each logged in help item in the menu.
        foreach ($helpmenu->get_children() as $child) {
            self::add_custom_menu_item($help, $child);
        }
    }

    /**
     * Add recursively an item to a custom menu item.
     *
     * @param custom_menu_item $menu The menu who will contains the item to add.
     * @param custom_menu_item $item The item to add in the menu.
     */
    private static function add_custom_menu_item(custom_menu_item $menu, custom_menu_item $item) {
        $newitem = $menu->add($item->get_text(), $item->get_url(), $item->get_title());
        foreach ($item->get_children() as $child) {
            self::add_custom_menu_item($newitem, $child);
        }
    }

    /**
     * Renders a custom menu object (located in outputcomponents.php)
     *
     * The custom menu this method produces makes use of the YUI3 menunav widget
     * and requires very specific html elements and classes.
     *
     * @param custom_menu $menu The custom menu to render.
     * @return string The html fragment of the menu.
     */
    protected function render_custom_menu(custom_menu $menu) {

        // Add "Home" in the menu.
        $menu->add(get_string('home'), new moodle_url('/?redirect=0'), get_string('home'), -5);

        // Add "My courses" in the menu.
        $menu->add(get_string('frontpagecourselist'), new moodle_url('/course'), get_string('frontpagecourselist'), -2);

        if (isloggedin() && !isguestuser()) {
            // Add "My courses" items in the menu.
            $branchtitle = get_string('mycourses');
            $branchurl = new moodle_url('/my/index.php');
            $branch = $menu->add($branchtitle, $branchurl, $branchtitle, -3);
            if ($my = udem_enrol_get_my_courses_sorted_by_session()) {
                static $maxitems = 10;
                $itemid = 0;
                foreach ($my as $mycourse) {
                    $param = array('id' => $mycourse->id);
                    if (!$mycourse->visible) {
                        $param[self::HIDDEN_COURSE] = 1;
                    }
                    $branch->add($mycourse->shortname, new moodle_url('/course/view.php', $param), $mycourse->fullname);
                    $itemid++;
                    if ($itemid >= $maxitems) {
                        $showall = get_string('showallmycourses', 'theme_cleanudem');
                        $branch->add(self::DIVIDER);
                        $branch->add($showall, $branchurl, $showall);
                        break;
                    }
                }
                if ($itemid > 0 && !theme_cleanudem_is_default_device_type()) {
                    $menu->add(get_string('myhome'), new moodle_url('/my/index.php'), get_string('myhome'), -3);
                }
            }
        }

        return parent::render_custom_menu($menu);
    }

    /**
     * This code renders the custom menu items for the
     * bootstrap dropdown menu.
     *
     * @param custom_menu_item $menunode The menu node containing the item to render.
     * @param int $level The position where render the menu.
     * @return string The html fragment of the menu item.
     */
    protected function render_custom_menu_item(custom_menu_item $menunode, $level = 0) {
        global $USER;

        static $submenucount = 0;

        $target = theme_cleanudem_get_target($menunode->get_url());

        if ($menunode->has_children()) {

            if ($level == 1) {
                $class = 'dropdown';
                if ($isusermenu = $menunode->get_text() == fullname($USER)) {
                    $class .= ' usermenu';
                }
            } else {
                $class = 'dropdown-submenu';
            }

            if ($menunode === $this->language) {
                $class .= ' langmenu';
            }
            $content = html_writer::start_tag('li', array('class' => $class));
            // If the child has menus render it as a sub menu.
            $submenucount++;

            if ($menunode->get_url() !== null) {
                $url = $menunode->get_url();
            } else {
                $url = '#cm_submenu_' . $submenucount;
            }

            $datatoggle = 'dropdown';

            if (theme_cleanudem_is_default_device_type() && $menunode->get_title() != get_string('language')) {
                $datatoggle = '';
            }

            $content .= html_writer::start_tag('a', array('href' => $url, 'target' => $target , 'class' => 'dropdown-toggle',
                'data-toggle' => $datatoggle, 'title' => $menunode->get_title()));
            $content .= $menunode->get_text();
            if ($level == 1) {
                $content .= html_writer::tag('b', '', array('class' => 'caret'));
                if ($isusermenu) {
                    $size = 30;
                    if (!theme_cleanudem_is_default_device_type()) {
                        $size *= 2;
                    }
                    $content .= $this->user_picture($USER, array('link' => false, 'size' => $size, 'alttext' => true));
                }
            }
            $content .= html_writer::end_tag('a');
            $content .= html_writer::start_tag('ul', array('class' => 'dropdown-menu'));
            foreach ($menunode->get_children() as $menunode) {
                $content .= $this->render_custom_menu_item($menunode, 0);
            }
            $content .= html_writer::end_tag('ul');
        } else {
            if ($menunode->get_text() == self::DIVIDER) {
                return html_writer::start_tag('li', array('class' => self::DIVIDER));
            }
            $content = html_writer::start_tag('li');
            // The node doesn't have children so produce a final menuitem.
            if ($menunode->get_url() !== null) {
                $url = $menunode->get_url();
                $title = $menunode->get_text();
                $class = '';
                if (array_key_exists(self::HIDDEN_COURSE, $url->params())) {
                    $class = 'dimmed';
                    $title = udem_add_unavailable_course_suffix($title);
                    $url->remove_params(self::HIDDEN_COURSE);
                }
            } else {
                $url = '#';
            }
            $content .= html_writer::link($url, $title, array('title' => $menunode->get_title(),
                'target' => $target, 'class' => $class));
        }
        return $content;
    }

    /**
     * Construct a user menu, returning HTML that can be echoed out by a
     * layout file.
     *
     * @param stdClass $user A user object, usually $USER.
     * @param bool $withlinks true if a dropdown should be built.
     * @return string The html fragment of the user menu.
     */
    public function user_menu($user = null, $withlinks = null) {
        $usermenu = new custom_menu('', current_language());
        return $this->render_user_menu($usermenu);
    }

    /**
     * Renders a custom menu object for the user menu.
     *
     * @param custom_menu $menu The custom menu used for adding items.
     * @return string The html fragment of the user menu.
     */
    protected function render_user_menu(custom_menu $menu) {
        global $USER;
        $content = '';
        define('STUDENT_ROLE_ID', 5);
        if (isloggedin() && !isguestuser()) {
            // Add The user menu.
            $fullname = fullname($USER);
            $usermenu = $menu->add($fullname, new moodle_url('#'), $fullname, 10001);

            // My home.
            $my = get_string('myhome');
            $usermenu->add($my, new moodle_url('/my/index.php'), $my);

            $usermenu->add(self::DIVIDER);

            // Profile.
            $profile = get_string('profile');
            $usermenu->add($profile, new moodle_url('/user/profile.php'), $profile);

            // Grades (only if the user is a student in at least one course).
            if (theme_cleanudem_user_has_role_assignment($USER->id, STUDENT_ROLE_ID)) {
                $grades = get_string('grades');
                $usermenu->add($grades, new moodle_url('/grade/report/overview/index.php'), $grades);
            }

            // Forum posts.
            $forumpost = get_string('forumposts', 'forum');
            $usermenu->add($forumpost, new moodle_url('/mod/forum/user.php'), $forumpost);

            // Messages.
            $message = get_string('messages', 'message');
            $usermenu->add($message, new moodle_url('/message/index.php'), $message);

            // My files.
            $myfiles = get_string('privatefiles');
            $usermenu->add($myfiles, new moodle_url('/user/files.php'), $myfiles);

            // My preferences.
            $mypreferences = get_string('preferences', 'moodle');
            $usermenu->add($mypreferences, new moodle_url('/user/preferences.php'), $mypreferences);

            $usermenu->add(self::DIVIDER);

            // Logout.
            $logout = get_string('logout');
            $usermenu->add($logout, new moodle_url('/login/logout.php', array('sesskey' => sesskey(), 'alt' => 'logout')), $logout);

            $content .= html_writer::start_tag('ul', array('class' => 'nav'));
            foreach ($menu->get_children() as $item) {
                $content .= $this->render_custom_menu_item($item, 1);
            }

            return $content . html_writer::end_tag('ul');
        }
        return $content;
    }

    /**
     * Add the login buttons, CAS and No CAS.
     */
    public function login_buttons() {
        global $OUTPUT;
        $content = '';
        if (!isloggedin()) {
            $loginpage = ((string)$this->page->url === get_login_url());
            if ($loginpage) {
                $content .= html_writer::div(get_string('loggedinnot', 'moodle'), 'navbar-text logininfo pull-right');
            } else {
                $url = new moodle_url(get_login_url());
                $method = 'get';
                $content .= html_writer::start_div('login-buttons pull-right');
                $url->param('authCAS', 'CAS');
                $content .= $OUTPUT->single_button($url, get_string('acceslogincas', 'local_custompages'), $method,
                        array('class' => 'login login-cas buttonemphasis',
                        'tooltip' => get_string('acceslogincastitle', 'local_custompages')));
                if (udem_is_multiauth_cas()) {
                    $url->param('authCAS', 'NOCAS');
                    $content .= $OUTPUT->single_button($url, get_string('accesloginnocas', 'local_custompages'), $method,
                            array('class' => 'login login-nocas',
                                'tooltip' => get_string('accesloginnocastitle', 'local_custompages')));
                    $content .= $OUTPUT->help_icon('accesloginnocas', 'local_custompages');
                }
                $content .= html_writer::end_div();
            }
        }
        return $content;
    }

    /**
     * Add a fullscreen button.
     *
     * @param boolean $state The state of the fullscreen button.
     * @return string $content The html fragment of the button.
     */
    public function fullscreen_button($state = false) {
        $content = '';
        if (isloggedin() && !isguestuser()) {
            $string = 'enablefullscreenmode';
            $statestring = 'true';
            if ($state) {
                $string = 'disablefullscreenmode';
                $statestring = 'false';
            }
            $enable = html_writer::span('', 'fa fa-compress');
            $disable = html_writer::span('', 'fa fa-expand');
            $url = new moodle_url($this->page->url, array('fullscreenmodestate' => $statestring));
            $content = html_writer::link($url, $enable . $disable, array('title' => get_string($string, 'theme_cleanudem'),
                'class' => 'navbar-text fullscreen-toggle-btn'));
        }
        return $content;
    }

    /**
     * Generate the frontpage slide show.
     *
     * @return string The generated html fragment.
     */
    protected function slideshow() {
        $numberofslides = $this->page->theme->settings->numberofslides;
        $slideinterval = $this->page->theme->settings->slideinterval;
        $html = '';
        if (!empty($numberofslides)) {
            if ($numberofslides > 1) {
                $html .= html_writer::start_tag('ol', array('class' => 'carousel-indicators'));
                for ($s = 0; $s < $numberofslides; $s++) {
                    $class = '';
                    if ($s == 0) {
                        $class = 'active';
                    }
                    $html .= html_writer::tag('li', '', array('data-target' => '#cleanudemCarousel', 'data-slide-to' => $s,
                            'class' => $class));
                }
                $html .= html_writer::end_tag('ol');
            }
            $html .= html_writer::start_div('carousel-inner');
            for ($s = 1; $s <= $numberofslides; $s++) {
                $html .= $this->slide($s);
            }
            $html .= html_writer::end_div();

            if ($numberofslides > 1) {
                $html .= html_writer::link('#cleanudemCarousel', html_writer::tag('i', '', array('class' => 'fa fa-chevron-left')),
                        array('class' => 'left carousel-control', 'data-slide' => 'prev',
                        'title' => get_string('previousslide', 'theme_cleanudem')));
                $html .= html_writer::link('#cleanudemCarousel', html_writer::tag('i', '', array('class' => 'fa fa-chevron-right')),
                        array('class' => 'right carousel-control', 'data-slide' => 'next',
                        'title' => get_string('nextslide', 'theme_cleanudem')));
            }
            $attributes = array('id' => 'cleanudemCarousel');
            if (!empty($slideinterval)) {
                $attributes['data-interval'] = $slideinterval;
            }
            $html = $this->theme_edit_button('theme_cleanudem_slideshow') . html_writer::div($html, 'carousel slide', $attributes);
            $html = html_writer::div($html, 'span12');
            $html = html_writer::div($html, 'row-fluid');
            $html = html_writer::tag('section', $html, array('class' => 'slideshow'));
        }
        return $html;
    }

    /**
     * Generate the output for a particular slide.
     *
     * @param int $id The id of the current slide.
     * @return string The generated html fragment for one slide.
     */
    private function slide($id) {
        $slideuseplaybuttonaslink = theme_cleanudem_get_setting('slideuseplaybuttonaslink' . $id );
        $slideurl = theme_cleanudem_get_setting('slide' . $id .'url');
        $slideurltarget = theme_cleanudem_get_setting('slide' . $id .'target');
        $slidetitle = theme_cleanudem_get_setting('slide'.$id, true);
        $slideclasses = array('item');
        if ($id === 1) {
            $slideclasses[] = 'active';
        }

        $slide = '';
        // Output if at least the title is present.
        if ($slidetitle) {
            $label = '';
            if ($slidetitle) {
                $label = $slidetitle;
                if ($slideurl && empty($slideuseplaybuttonaslink)) {
                    $label = html_writer::link($slideurl, $label, array('target' => $slideurltarget, 'title' => $label));
                }
                $slide .= $this->heading($label, 4);
            }
            if ($slideurl && !empty($slideuseplaybuttonaslink)) {
                $playicon = html_writer::tag('i', '', array('class' => 'fa fa-play'));
                $label = get_string('playclip', 'theme_cleanudem', $label);
                $slide .= html_writer::link($slideurl, $playicon, array('class' => 'play-button', 'target' => $slideurltarget,
                        'title' => $label));
            }

            $slide = html_writer::div($slide, 'carousel-caption-inner');
            $slide = html_writer::div($slide, 'carousel-caption');
        }

        $params = array();
        if (theme_cleanudem_get_setting('slide' . $id .'image')) {
            $slideimage = $this->page->theme->setting_file_url('slide' . $id .'image', 'slide' . $id . 'image');
            $params['style'] = 'background-image: url(' . $slideimage . ')';
        }
        $slide = html_writer::div($slide, 'slider-size', $params);
        $slide = html_writer::div($slide, implode(' ', $slideclasses));
        return $slide;
    }

    /**
     * Add a button to edit the settings for a partocular section of the theme.
     *
     * @param string $section The section name to open.
     * @return string The generated html fragment for one slide.
     */
    protected function theme_edit_button($section) {
        global $CFG;
        if ($this->page->user_is_editing() && is_siteadmin()) {
            $edit = get_string('edit');
            return html_writer::link($CFG->wwwroot . '/admin/settings.php?section=' . $section, $edit,
                    array('class' => 'pull-right editsection', 'title' => $edit));
        }
    }

    /**
     * Layout elements.
     *
     * This renderer does not override any existing renderer but provides a way of including
     * portion of files into your layout pages. Those portions are called 'elements' and are
     * located in the directory layout/elements of your theme.
     *
     * To include one of those elements in your layout (or other elements), use this:
     *
     *   <?php echo $OUTPUT->element('elementNameWithoutDotPHP'); ?>
     *
     * You can also pass some variables to your elements, by passing an array as the second argument.
     *
     *   $myvars = array('var1' => 'Hello', 'var2' => 'World');
     *   echo $OUTPUT->element('elementNameWithoutDotPHP', $myvars);
     *
     * Then, you can simply use the variables in your element, in our example your element could be:
     *
     *   <h1><?php echo $var1; ?> <?php echo $var2; ?></h1>
     *
     * You do not need to pass $CFG, $OUTPUT or $VARS, they are made available for you.
     *
     * @param string $name of the element, without .php.
     * @param array $vars associative array of variables.
     * @return string
     */
    public function element($name, $vars = array()) {
        $OUTPUT = $this;
        $PAGE = $this->page;
        $COURSE = $this->page->course;

        $element = $name . '.php';
        $candidate = $this->page->theme->dir . '/layout/elements/' . $element;
        if (!is_readable($candidate)) {
            debugging("Could not include element $name.");
            return '';
        }

        ob_start();
        include($candidate);
        $output = ob_get_clean();
        return $output;
    }
}
