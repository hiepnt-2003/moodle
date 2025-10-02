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
 * Library functions for User Activity Log report plugin.
 *
 * @package    report_useractivitylog
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * This function extends the navigation with the report items
 *
 * @param navigation_node $navigation The navigation node to extend
 * @param stdClass $course The course to object for the report
 * @param stdClass $context The context of the course
 */
function report_useractivitylog_extend_navigation_course($navigation, $course, $context) {
    if (has_capability('report/useractivitylog:view', $context)) {
        $url = new moodle_url('/report/useractivitylog/index.php', array('id' => $course->id));
        $navigation->add(get_string('pluginname', 'report_useractivitylog'), $url, 
                        navigation_node::TYPE_SETTING, null, null, new pix_icon('i/report', ''));
    }
}

/**
 * This function extends the course administration navigation
 *
 * @param navigation_node $navigation The navigation node to extend
 * @param stdClass $course The course to object for the report
 * @param stdClass $context The context of the course
 */
function report_useractivitylog_extend_navigation_user_settings($navigation, $user, $usercontext, $course, $coursecontext) {
    if (has_capability('report/useractivitylog:view', $coursecontext)) {
        $url = new moodle_url('/report/useractivitylog/index.php', array('id' => $course->id, 'userid' => $user->id));
        $navigation->add(get_string('pluginname', 'report_useractivitylog'), $url);
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
function report_useractivitylog_myprofile_navigation(core_user\output\myprofile\tree $tree, $user, $iscurrentuser, $course) {
    if (isguestuser($user) || !$course) {
        return true;
    }
    
    $context = context_course::instance($course->id);
    if (has_capability('report/useractivitylog:view', $context)) {
        $url = new moodle_url('/report/useractivitylog/index.php', 
                             array('id' => $course->id, 'userid' => $user->id));
        $node = new core_user\output\myprofile\node('reports', 'useractivitylog', 
                                                   get_string('pluginname', 'report_useractivitylog'), 
                                                   null, $url);
        $tree->add_node($node);
    }
    
    return true;
}