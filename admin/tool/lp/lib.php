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
 * This page contains navigation hooks for learning plans.
 *
 * @package    tool_lp
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/externallib.php');

/**
 * This function extends the course navigation
 *
 * @param navigation_node $navigation The navigation node to extend
 * @param stdClass $course The course to object for the tool
 * @param context $coursecontext The context of the course
 */
function tool_lp_extend_navigation_course($navigation, $course, $coursecontext) {
    if (!\tool_lp\api::is_enabled()) {
        return;
    }

    // Just a link to course report.
    $title = get_string('coursecompetencies', 'tool_lp');
    $path = new moodle_url("/admin/tool/lp/coursecompetencies.php", array('courseid' => $course->id));
    $settingsnode = navigation_node::create($title,
                                            $path,
                                            navigation_node::TYPE_SETTING,
                                            null,
                                            null,
                                            new pix_icon('competency', '', 'tool_lp'));
    if (isset($settingsnode)) {
        $navigation->add_node($settingsnode);
    }
}


/**
 * This function extends the user navigation.
 *
 * @param navigation_node $navigation The navigation node to extend
 * @param stdClass $user The user object
 * @param context_user $usercontext The user context
 * @param stdClass $course The course object
 * @param context_course $coursecontext The context of the course
 */
function tool_lp_extend_navigation_user($navigation, $user, $usercontext, $course, $coursecontext) {
    if (!\tool_lp\api::is_enabled()) {
        return;
    }

    if (\tool_lp\plan::can_read_user($user->id)) {
        $node = $navigation->add(get_string('learningplans', 'tool_lp'),
            new moodle_url('/admin/tool/lp/plans.php', array('userid' => $user->id)));
    }

    if (\tool_lp\user_evidence::can_read_user($user->id)) {
        $node->add(get_string('userevidence', 'tool_lp'),
            new moodle_url('/admin/tool/lp/user_evidence_list.php', array('userid' => $user->id)));
    }
}

/**
 * Add nodes to myprofile page.
 *
 * @param \core_user\output\myprofile\tree $tree Tree object
 * @param stdClass $user user object
 * @param bool $iscurrentuser
 * @param stdClass $course Course object
 *
 * @return bool
 */
function tool_lp_myprofile_navigation(core_user\output\myprofile\tree $tree, $user, $iscurrentuser, $course) {
    if (!\tool_lp\api::is_enabled()) {
        return false;
    } else if (!\tool_lp\plan::can_read_user($user->id)) {
        return false;
    }

    $url = new moodle_url('/admin/tool/lp/plans.php', array('userid' => $user->id));
    $node = new core_user\output\myprofile\node('miscellaneous', 'learningplans',
                                                get_string('learningplans', 'tool_lp'), null, $url);
    $tree->add_node($node);

    return true;
}

/**
 * This function extends the category navigation to add learning plan links.
 *
 * @param navigation_node $navigation The navigation node to extend
 * @param context $coursecategorycontext The context of the course category
 */
function tool_lp_extend_navigation_category_settings($navigation, $coursecategorycontext) {
    if (!\tool_lp\api::is_enabled()) {
        return false;
    }

    // We check permissions before renderring the links.
    $templatereadcapability = \tool_lp\template::can_read_context($coursecategorycontext);
    $competencymanagecapability = has_capability('tool/lp:competencymanage', $coursecategorycontext);
    if (!$templatereadcapability && !$competencymanagecapability) {
        return false;
    }

    // The link to the learning plan page.
    if ($templatereadcapability) {
        $title = get_string('templates', 'tool_lp');
        $path = new moodle_url("/admin/tool/lp/learningplans.php", array('pagecontextid' => $coursecategorycontext->id));
        $settingsnode = navigation_node::create($title,
                                                $path,
                                                navigation_node::TYPE_SETTING,
                                                null,
                                                null,
                                                new pix_icon('competency', '', 'tool_lp'));
        if (isset($settingsnode)) {
            $navigation->add_node($settingsnode);
        }
    }

    // The link to the competency frameworks page.
    if ($competencymanagecapability) {
        $title = get_string('competencyframeworks', 'tool_lp');
        $path = new moodle_url("/admin/tool/lp/competencyframeworks.php", array('pagecontextid' => $coursecategorycontext->id));
        $settingsnode = navigation_node::create($title,
                                                $path,
                                                navigation_node::TYPE_SETTING,
                                                null,
                                                null,
                                                new pix_icon('competency', '', 'tool_lp'));
        if (isset($settingsnode)) {
            $navigation->add_node($settingsnode);
        }
    }
}


/**
 * File serving.
 *
 * @param stdClass $course The course object.
 * @param stdClass $cm The cm object.
 * @param context $context The context object.
 * @param string $filearea The file area.
 * @param array $args List of arguments.
 * @param bool $forcedownload Whether or not to force the download of the file.
 * @param array $options Array of options.
 * @return void|false
 */
function tool_lp_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    global $CFG;

    if (!\tool_lp\api::is_enabled()) {
        return false;
    }

    $fs = get_file_storage();
    $file = null;

    $itemid = array_shift($args);
    $filename = array_shift($args);
    $filepath = $args ? '/' .implode('/', $args) . '/' : '/';

    if ($filearea == 'userevidence' && $context->contextlevel == CONTEXT_USER) {
        if (\tool_lp\user_evidence::can_read_user($context->instanceid)) {
            $file = $fs->get_file($context->id, 'tool_lp', $filearea, $itemid, $filepath, $filename);
        }
    }

    if (!$file) {
        return false;
    }

    send_stored_file($file, null, 0, $forcedownload);
}

/**
 * Hook when a comment is added.
 *
 * @param  stdClass $comment The comment.
 * @param  stdClass $params The parameters.
 * @return array
 */
function tool_lp_comment_add($comment, $params) {
    global $USER;

    if (!\tool_lp\api::is_enabled()) {
        return;
    }

    if ($params->commentarea == 'user_competency') {
        $uc = new \tool_lp\user_competency($params->itemid);

        // Message both the user and the reviewer, except when they are the author of the message.
        $recipients = array($uc->get_userid());
        if ($uc->get_reviewerid()) {
            $recipients[] = $uc->get_reviewerid();
        }
        $recipients = array_diff($recipients, array($comment->userid));
        if (empty($recipients)) {
            return;
        }

        // Get the sender.
        $user = $USER;
        if ($USER->id != $comment->userid) {
            $user = core_user::get_user($comment->userid);
        }
        $fullname = fullname($user);

        // Get the competency.
        $competency = $uc->get_competency();
        $competencyname = format_string($competency->get_shortname(), true, array('context' => $competency->get_context()));

        // We want to send a message for one plan, trying to find an active one first, or the last modified one.
        $plan = null;
        $plans = $uc->get_plans();
        foreach ($plans as $candidate) {
            if ($candidate->get_status() == \tool_lp\plan::STATUS_ACTIVE) {
                $plan = $candidate;
                break;

            } else if (!empty($plan) && $plan->get_timemodified() < $candidate->get_timemodified()) {
                $plan = $candidate;

            } else if (empty($plan)) {
                $plan = $candidate;
            }
        }

        // Urls.
        // TODO MDL-52749 Replace the link to the plan with the user competency page.
        if (empty($plan)) {
            $urlname = get_string('userplans', 'tool_lp');
            $url = new moodle_url('/admin/tool/lp/plans.php', array('userid' => $uc->get_userid()));
        } else {
            $urlname = $competencyname;
            $url = new moodle_url('/admin/tool/lp/user_competency_in_plan.php', array(
                'userid' => $uc->get_userid(),
                'competencyid' => $uc->get_competencyid(),
                'planid' => $plan->get_id()
            ));
        }

        // Construct the message content.
        $fullmessagehtml = get_string('usercommentedonacompetencyhtml', 'tool_lp', array(
            'fullname' => $fullname,
            'competency' => $competencyname,
            'comment' => format_text($comment->content, $comment->format, array('context' => $params->context->id)),
            'url' => $url->out(true),
            'urlname' => $urlname,
        ));
        if ($comment->format == FORMAT_PLAIN || $comment->format == FORMAT_MOODLE) {
            $format = FORMAT_MOODLE;
            $fullmessage = get_string('usercommentedonacompetency', 'tool_lp', array(
                'fullname' => $fullname,
                'competency' => $competencyname,
                'comment' => $comment->content,
                'url' => $url->out(false),
            ));
        } else {
            $format = FORMAT_HTML;
            $fullmessage = $fullmessagehtml;
        }

        $message = new \core\message\message();
        $message->component = 'tool_lp';
        $message->name = 'user_competency_comment';
        $message->notification = 1;
        $message->userfrom = core_user::get_noreply_user();
        $message->subject = get_string('usercommentedonacompetencysubject', 'tool_lp', $fullname);
        $message->fullmessage = $fullmessage;
        $message->fullmessageformat = $format;
        $message->fullmessagehtml = $fullmessagehtml;
        $message->smallmessage = get_string('usercommentedonacompetencysmall', 'tool_lp', array(
            'fullname' => $fullname,
            'competency' => $competencyname,
        ));
        $message->contexturl = $url->out(false);
        $message->contexturlname = $urlname;

        // Message each recipient.
        foreach ($recipients as $recipient) {
            $msgcopy = clone($message);
            $msgcopy->userto = $recipient;
            message_send($msgcopy);
        }

    } else if ($params->commentarea == 'plan') {
        $plan = new \tool_lp\plan($params->itemid);

        // Message both the user and the reviewer, except when they are the author of the message.
        $recipients = array($plan->get_userid());
        if ($plan->get_reviewerid()) {
            $recipients[] = $plan->get_reviewerid();
        }
        $recipients = array_diff($recipients, array($comment->userid));
        if (empty($recipients)) {
            return;
        }

        // Get the sender.
        $user = $USER;
        if ($USER->id != $comment->userid) {
            $user = core_user::get_user($comment->userid);
        }

        $fullname = fullname($user);
        $planname = format_string($plan->get_name(), true, array('context' => $plan->get_context()));
        $urlname = $planname;
        $url = new moodle_url('/admin/tool/lp/plan.php', array(
            'id' => $plan->get_id()
        ));

        // Construct the message content.
        $fullmessagehtml = get_string('usercommentedonaplanhtml', 'tool_lp', array(
            'fullname' => $fullname,
            'plan' => $planname,
            'comment' => format_text($comment->content, $comment->format, array('context' => $params->context->id)),
            'url' => $url->out(true),
            'urlname' => $urlname,
        ));
        if ($comment->format == FORMAT_PLAIN || $comment->format == FORMAT_MOODLE) {
            $format = FORMAT_MOODLE;
            $fullmessage = get_string('usercommentedonaplan', 'tool_lp', array(
                'fullname' => $fullname,
                'plan' => $planname,
                'comment' => $comment->content,
                'url' => $url->out(false),
            ));
        } else {
            $format = FORMAT_HTML;
            $fullmessage = $fullmessagehtml;
        }

        $message = new \core\message\message();
        $message->component = 'tool_lp';
        $message->name = 'plan_comment';
        $message->notification = 1;
        $message->userfrom = core_user::get_noreply_user();
        $message->subject = get_string('usercommentedonaplansubject', 'tool_lp', $fullname);
        $message->fullmessage = $fullmessage;
        $message->fullmessageformat = $format;
        $message->fullmessagehtml = $fullmessagehtml;
        $message->smallmessage = get_string('usercommentedonaplansmall', 'tool_lp', array(
            'fullname' => $fullname,
            'plan' => $planname,
        ));
        $message->contexturl = $url->out(false);
        $message->contexturlname = $urlname;

        // Message each recipient.
        foreach ($recipients as $recipient) {
            $msgcopy = clone($message);
            $msgcopy->userto = $recipient;
            message_send($msgcopy);
        }
    }
}

/**
 * Return the permissions of for the comments.
 *
 * @param  stdClass $params The parameters.
 * @return array
 */
function tool_lp_comment_permissions($params) {
    if (!\tool_lp\api::is_enabled()) {
        return array('post' => false, 'view' => false);
    }

    if ($params->commentarea == 'user_competency') {
        $uc = new \tool_lp\user_competency($params->itemid);
        if ($uc->can_read()) {
            return array('post' => $uc->can_comment(), 'view' => $uc->can_read_comments());
        }
    } else if ($params->commentarea == 'plan') {
        $plan = new \tool_lp\plan($params->itemid);
        if ($plan->can_read()) {
            return array('post' => $plan->can_comment(), 'view' => $plan->can_read_comments());
        }
    }

    return array('post' => false, 'view' => false);
}

/**
 * Validates comments.
 *
 * @param  stdClass $params The parameters.
 * @return bool
 */
function tool_lp_comment_validate($params) {
    if (!\tool_lp\api::is_enabled()) {
        return false;
    }

    if ($params->commentarea == 'user_competency') {
        if (!\tool_lp\user_competency::record_exists($params->itemid)) {
            return false;
        }
        return true;
    } else if ($params->commentarea == 'plan') {
        if (!\tool_lp\plan::record_exists($params->itemid)) {
            return false;
        }
        return true;
    }
    return false;
}

/**
 * Inject the competencies elements into all moodle module settings forms.
 *
 * @param moodleform $formwrapper The moodle quickforms wrapper object.
 * @param MoodleQuickForm $mform The actual form object (required to modify the form).
 */
function tool_lp_coursemodule_standard_elements($formwrapper, $mform) {
    global $CFG, $COURSE;

    if (!\tool_lp\api::is_enabled()) {
        return;
    } else if (!has_capability('tool/lp:coursecompetencymanage', $formwrapper->get_context())) {
        return;
    }

    $mform->addElement('header', 'competenciessection', get_string('competencies', 'tool_lp'));

    MoodleQuickForm::registerElementType('course_competencies',
                                         "$CFG->dirroot/admin/tool/lp/classes/course_competencies_form_element.php",
                                         'tool_lp_course_competencies_form_element');
    $cmid = null;
    if ($cm = $formwrapper->get_coursemodule()) {
        $cmid = $cm->id;
    }
    $options = array(
        'courseid' => $COURSE->id,
        'cmid' => $cmid
    );
    $mform->addElement('course_competencies', 'competencies', get_string('modcompetencies', 'tool_lp'), $options);
    $mform->addHelpButton('competencies', 'modcompetencies', 'tool_lp');
    MoodleQuickForm::registerElementType('course_competency_rule',
                                         "$CFG->dirroot/admin/tool/lp/classes/course_competency_rule_form_element.php",
                                         'tool_lp_course_competency_rule_form_element');
    // Reuse the same options.
    $mform->addElement('course_competency_rule', 'competency_rule', get_string('uponcoursemodulecompletion', 'tool_lp'), $options);
}

/**
 * Hook the add/edit of the course module.
 *
 * @param stdClass $data Data from the form submission.
 * @param stdClass $course The course.
 */
function tool_lp_coursemodule_edit_post_actions($data, $course) {
    if (!\tool_lp\api::is_enabled()) {
        return $data;
    }

    // It seems like the form did not contain any of the form fields, we can return.
    if (!isset($data->competency_rule) && !isset($data->competencies)) {
        return $data;
    }

    // We bypass the API here and go direct to the persistent layer - because we don't want to do permission
    // checks here - we need to load the real list of existing course module competencies.
    $existing = \tool_lp\course_module_competency::list_course_module_competencies($data->coursemodule);

    $existingids = array();
    foreach ($existing as $cmc) {
        array_push($existingids, $cmc->get_competencyid());
    }

    $newids = isset($data->competencies) ? $data->competencies : array();

    $removed = array_diff($existingids, $newids);
    $added = array_diff($newids, $existingids);

    foreach ($removed as $removedid) {
        \tool_lp\api::remove_competency_from_course_module($data->coursemodule, $removedid);
    }
    foreach ($added as $addedid) {
        \tool_lp\api::add_competency_to_course_module($data->coursemodule, $addedid);
    }

    if (isset($data->competency_rule)) {
        // Now update the rules for each course_module_competency.
        $current = \tool_lp\api::list_course_module_competencies_in_course_module($data->coursemodule);
        foreach ($current as $coursemodulecompetency) {
            \tool_lp\api::set_course_module_competency_ruleoutcome($coursemodulecompetency, $data->competency_rule);
        }
    }

    return $data;
}
