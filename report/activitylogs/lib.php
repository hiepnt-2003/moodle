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
 * Hiển thị bảng logs
 *
 * @param int $userid User ID (0 for all users)
 * @param int $courseid Course ID (0 for all courses)
 * @param int $datefrom Start timestamp
 * @param int $dateto End timestamp
 */
function report_activitylogs_display_logs_table($userid, $courseid, $datefrom, $dateto) {
    global $DB, $OUTPUT;
    
    // Build SQL query
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
            WHERE l.timecreated >= :datefrom 
            AND l.timecreated <= :dateto";
    
    $params = array(
        'datefrom' => $datefrom,
        'dateto' => $dateto
    );
    
    if ($userid > 0) {
        $sql .= " AND l.userid = :userid";
        $params['userid'] = $userid;
    }
    
    if ($courseid > 0) {
        $sql .= " AND ctx.instanceid = :courseid AND ctx.contextlevel = 50";
        $params['courseid'] = $courseid;
    }
    
    $sql .= " ORDER BY l.timecreated DESC";
    
    // Get logs
    $logs = $DB->get_records_sql($sql, $params, 0, 1000); // Limit to 1000 records
    
    if (empty($logs)) {
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
    
    foreach ($logs as $log) {
        $row = array();
        
        // Time
        $timecreated = isset($log->timecreated) ? $log->timecreated : time();
        $row[] = userdate($timecreated, get_string('strftimedatetime', 'langconfig'));
        
        // User full name
        if (isset($log->firstname) && isset($log->lastname) && $log->firstname && $log->lastname) {
            $userlink = html_writer::link(
                new moodle_url('/user/profile.php', array('id' => $log->userid)),
                fullname($log)
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
                'firstname' => $log->relatedfirstname,
                'lastname' => $log->relatedlastname
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
    
    echo html_writer::table($table);
    
    // Display count
    $count = count($logs);
    echo html_writer::tag('p', get_string('numberofentries', 'moodle', $count), array('class' => 'log-count'));
}

/**
 * Lấy description từ event
 *
 * @param object $log Log entry object
 * @return string Event description
 */
function report_activitylogs_get_event_description($log) {
    $description = '';
    
    try {
        // Try to get event description
        if (isset($log->eventname) && class_exists($log->eventname)) {
            $eventclass = $log->eventname;
            
            // Prepare context safely
            $eventcontext = null;
            try {
                if (isset($log->contextid)) {
                    $eventcontext = context::instance_by_id($log->contextid, IGNORE_MISSING);
                }
            } catch (Exception $e) {
                try {
                    $eventcontext = context_system::instance();
                } catch (Exception $e2) {
                    $eventcontext = null;
                }
            }
            
            if ($eventcontext) {
                $eventdata = [
                    'objectid' => isset($log->objectid) ? $log->objectid : null,
                    'context' => $eventcontext,
                    'userid' => isset($log->userid) ? $log->userid : 0,
                    'relateduserid' => isset($log->relateduserid) ? $log->relateduserid : null,
                    'other' => null
                ];
                
                // Try to unserialize 'other' data
                if (isset($log->other) && !empty($log->other)) {
                    try {
                        $eventdata['other'] = @unserialize($log->other);
                    } catch (Exception $e) {
                        // Keep other as null
                    }
                }
                
                try {
                    $event = $eventclass::create($eventdata);
                    $description = $event->get_description();
                } catch (Exception $e) {
                    // Event creation failed
                }
            }
        }
    } catch (Exception $e) {
        // Failed to get description from event class
    }
    
    // If still no description, create a generic one
    if (empty($description)) {
        $description = report_activitylogs_create_generic_description($log);
    }
    
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
