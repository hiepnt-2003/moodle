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
 * Library functions for Activity Logs report
 *
 * @package    report_activitylogs
 * @copyright  2025
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Hiển thị bảng logs với phân trang (50 logs/page)
 * Chỉ load dữ liệu của trang hiện tại để tối ưu hiệu suất
 *
 * @param array $userids Array of User IDs (empty array for all users)
 * @param array $courseids Array of Course IDs (empty array for all courses)
 * @param int $datefrom Start timestamp
 * @param int $dateto End timestamp
 * @param int $page Current page number (0-based)
 */
function report_activitylogs_display_logs_table($userids, $courseids, $datefrom, $dateto, $page = 0) {
    global $DB, $OUTPUT;
    
    // Pagination settings
    $perpage = 50; // 50 logs per page
    $offset = $page * $perpage;
    
    // Build WHERE clause and parameters
    $where = "l.timecreated >= :datefrom AND l.timecreated <= :dateto";
    $params = array(
        'datefrom' => $datefrom,
        'dateto' => $dateto
    );
    
    // Filter by multiple users
    if (!empty($userids) && is_array($userids)) {
        list($insql, $inparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, 'user');
        $where .= " AND l.userid $insql";
        $params = array_merge($params, $inparams);
    }
    
    // Filter by multiple courses
    if (!empty($courseids) && is_array($courseids)) {
        list($insql, $inparams) = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED, 'course');
        $where .= " AND ctx.instanceid $insql AND ctx.contextlevel = 50";
        $params = array_merge($params, $inparams);
    }
    
    // Step 1: Count total records (for pagination)
    $countsql = "SELECT COUNT(l.id)
                 FROM {logstore_standard_log} l
                 LEFT JOIN {context} ctx ON l.contextid = ctx.id
                 WHERE $where";
    
    $totalcount = $DB->count_records_sql($countsql, $params);
    
    if ($totalcount == 0) {
        echo $OUTPUT->notification(get_string('nologs', 'report_activitylogs'), 'info');
        return;
    }
    
    // Step 2: Get only records for current page
    $sql = "SELECT l.id, l.timecreated, l.userid, l.relateduserid, l.eventname, 
                   l.component, l.action, l.target, l.objecttable, l.objectid,
                   l.crud, l.edulevel, l.contextid, l.contextlevel, l.contextinstanceid,
                   l.ip, l.origin, l.other,
                   u.firstname, u.lastname, u.email,
                   ru.firstname as relatedfirstname, ru.lastname as relatedlastname,
                   c.fullname as coursename,
                   ctx.contextlevel, ctx.instanceid
            FROM {logstore_standard_log} l
            LEFT JOIN {user} u ON l.userid = u.id
            LEFT JOIN {user} ru ON l.relateduserid = ru.id
            LEFT JOIN {context} ctx ON l.contextid = ctx.id
            LEFT JOIN {course} c ON (ctx.contextlevel = 50 AND ctx.instanceid = c.id)
            WHERE $where
            ORDER BY l.timecreated DESC";
    
    // Get records for current page only
    $records = $DB->get_records_sql($sql, $params, $offset, $perpage);
    
    if (empty($records)) {
        echo $OUTPUT->notification(get_string('nologs', 'report_activitylogs'), 'info');
        return;
    }
    
    // Create table
    $table = new html_table();
    $table->head = array(
        get_string('time', 'report_activitylogs'),
        get_string('userfullname', 'report_activitylogs'),
        get_string('affecteduser', 'report_activitylogs'),
        get_string('eventcontext', 'report_activitylogs'),
        get_string('component', 'report_activitylogs'),
        get_string('eventname', 'report_activitylogs'),
        get_string('description'),
        get_string('origin', 'report_activitylogs'),
        get_string('ipaddress', 'report_activitylogs')
    );
    $table->attributes['class'] = 'generaltable';
    
    // Build table rows from records
    foreach ($records as $log) {
        $row = array();
        
        // Time
        $timecreated = isset($log->timecreated) ? $log->timecreated : time();
        $row[] = userdate($timecreated, get_string('strftimedatetime', 'langconfig'));
        
        // User full name
        if (isset($log->firstname) && isset($log->lastname) && $log->firstname && $log->lastname) {
            $user = (object)[
                'id' => isset($log->userid) ? $log->userid : 0,
                'firstname' => $log->firstname,
                'lastname' => $log->lastname,
                'firstnamephonetic' => '',
                'lastnamephonetic' => '',
                'middlename' => '',
                'alternatename' => ''
            ];
            $userlink = html_writer::link(
                new moodle_url('/user/profile.php', array('id' => $log->userid)),
                fullname($user)
            );
            $row[] = $userlink;
        } else {
            $row[] = '-';
        }
        
        // Affected user (related user)
        if (isset($log->relateduserid) && $log->relateduserid && 
            isset($log->relatedfirstname) && isset($log->relatedlastname) &&
            $log->relatedfirstname && $log->relatedlastname) {
            $relateduser = (object)[
                'id' => $log->relateduserid,
                'firstname' => $log->relatedfirstname,
                'lastname' => $log->relatedlastname,
                'firstnamephonetic' => '',
                'lastnamephonetic' => '',
                'middlename' => '',
                'alternatename' => ''
            ];
            $row[] = fullname($relateduser);
        } else {
            $row[] = '-';
        }
        
        // Event context (Course name)
        if (isset($log->coursename) && $log->coursename) {
            $contextlink = html_writer::link(
                new moodle_url('/course/view.php', array('id' => $log->instanceid)),
                $log->coursename
            );
            $row[] = $contextlink;
        } else {
            $row[] = '-';
        }
        
        // Component
        $component = isset($log->component) && !empty($log->component) ? $log->component : '-';
        $row[] = $component;
        
        // Event name - extract just the event name (last part)
        $eventname = isset($log->eventname) && !empty($log->eventname) ? $log->eventname : '-';
        if ($eventname != '-') {
            $parts = explode('\\', $eventname);
            $eventname = end($parts);
        }
        $row[] = $eventname;
        
        // Description
        $description = report_activitylogs_get_event_description($log);
        $row[] = $description;
        
        // Origin
        $origin = isset($log->origin) && $log->origin ? $log->origin : '-';
        $row[] = $origin;
        
        // IP Address
        $ip = isset($log->ip) && $log->ip ? $log->ip : '-';
        $row[] = $ip;
        
        $table->data[] = $row;
    }
    
    // Display table
    echo html_writer::table($table);
    
    // Display count and pagination
    $showing_from = $offset + 1;
    $showing_to = min($offset + $perpage, $totalcount);
    echo html_writer::tag('p', 
        get_string('showingentries', 'report_activitylogs', 
            array('from' => $showing_from, 'to' => $showing_to, 'total' => $totalcount)
        ), 
        array('class' => 'log-count')
    );
    
    // Display pagination bar
    $baseurl = new moodle_url('/report/activitylogs/index.php', array(
        'filtertype' => !empty($userids) ? 'user' : 'course',
        'datefrom' => $datefrom,
        'dateto' => $dateto
    ));
    
    // Add user or course IDs to URL
    if (!empty($userids)) {
        foreach ($userids as $userid) {
            $baseurl->param('userids[]', $userid);
        }
    }
    if (!empty($courseids)) {
        foreach ($courseids as $courseid) {
            $baseurl->param('courseids[]', $courseid);
        }
    }
    
    echo $OUTPUT->paging_bar($totalcount, $page, $perpage, $baseurl);
}

/**
 * Lấy description từ event
 *
 * @param object $log Log entry object
 * @return string Event description
 */
function report_activitylogs_get_event_description($log) {
    $description = '';
    
    // Bỏ qua việc tạo event description vì có thể gây lỗi
    // Trực tiếp tạo generic description thôi
    $description = report_activitylogs_create_generic_description($log);
    
    return $description;
}

/**
 * Tạo description
 *
 * @param object $log Log entry object
 * @return string Generic description
 */
function report_activitylogs_create_generic_description($log) {
    $description = 'The user';
    
    if (isset($log->userid)) {
        $description .= " with id '{$log->userid}'";
    }
    
    // Add action information
    if (isset($log->action) && !empty($log->action)) {
        $description .= " {$log->action}";
    }
    
    // Add target information
    if (isset($log->target) && !empty($log->target)) {
        $description .= " the {$log->target}";
    }
    
    // Add object information
    if (isset($log->objectid) && !empty($log->objectid)) {
        $description .= " with id '{$log->objectid}'";
    }
    
    // Add course context if available
    if (isset($log->coursename) && !empty($log->coursename)) {
        $description .= " in the course '{$log->coursename}'";
    }
    
    $description .= '.';
    
    return $description;
}
