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
 * Activity Logs report main page
 *
 * @package    report_activitylogs
 * @copyright  2025
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir.'/tablelib.php');

require_login();

$context = context_system::instance();
require_capability('report/activitylogs:view', $context);

$PAGE->set_url(new moodle_url('/report/activitylogs/index.php'));
$PAGE->set_context($context);
$PAGE->set_pagelayout('report');
$PAGE->set_title(get_string('pluginname', 'report_activitylogs'));
$PAGE->set_heading(get_string('pluginname', 'report_activitylogs'));

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pluginname', 'report_activitylogs'));

// Display filter form
$mform = new \report_activitylogs\form\filter_form();

// Get form data
$data = $mform->get_data();

// Display form first
$mform->display();

// Then display results below the form
if ($data) {
    // Process form data and display logs below form
    $userid = $data->userid;
    $courseid = $data->courseid;
    $datefrom = $data->datefrom;
    $dateto = $data->dateto;
    
    // Display the logs table below form
    display_logs_table($userid, $courseid, $datefrom, $dateto);
} else {
    echo html_writer::tag('p', get_string('selectcriteria', 'report_activitylogs'), array('class' => 'alert alert-info'));
}

echo $OUTPUT->footer();

/**
 * Display logs table based on filter criteria
 *
 * @param int $userid User ID (0 for all users)
 * @param int $courseid Course ID (0 for all courses)
 * @param int $datefrom Start timestamp
 * @param int $dateto End timestamp
 */
function display_logs_table($userid, $courseid, $datefrom, $dateto) {
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
        $row[] = userdate($log->timecreated, get_string('strftimedatetime', 'langconfig'));
        
        // User full name
        if ($log->firstname && $log->lastname) {
            $userlink = html_writer::link(
                new moodle_url('/user/profile.php', array('id' => $log->userid)),
                fullname($log)
            );
            $row[] = $userlink;
        } else {
            $row[] = '-';
        }
        
        // Affected user (related user)
        if ($log->relateduserid && $log->relatedfirstname && $log->relatedlastname) {
            $relateduser = (object)[
                'firstname' => $log->relatedfirstname,
                'lastname' => $log->relatedlastname
            ];
            $row[] = fullname($relateduser);
        } else {
            $row[] = '-';
        }
        
        // Event context (Course name)
        if ($log->coursename) {
            $contextlink = html_writer::link(
                new moodle_url('/course/view.php', array('id' => $log->instanceid)),
                $log->coursename
            );
            $row[] = $contextlink;
        } else {
            $row[] = '-';
        }
        
        // Component - show with more details
        $componenttext = $log->component ? $log->component : 'System';
        // Add action and target info if available
        if ($log->action || $log->target) {
            $componenttext .= '<br><small style="color: #666;">';
            if ($log->action) {
                $componenttext .= 'Action: ' . $log->action;
            }
            if ($log->target) {
                $componenttext .= ($log->action ? ' | ' : '') . 'Target: ' . $log->target;
            }
            $componenttext .= '</small>';
        }
        $row[] = $componenttext;
        
        // Event name - show full event name with additional info
        $eventname = $log->eventname;
        $eventdisplay = $eventname;
        
        // Add CRUD and Education level info
        if ($log->crud || $log->edulevel) {
            $eventdisplay .= '<br><small style="color: #666;">';
            $crudmap = array('c' => 'Create', 'r' => 'Read', 'u' => 'Update', 'd' => 'Delete');
            $edulevelmap = array(
                0 => 'Other',
                1 => 'Participating', 
                2 => 'Teaching',
                3 => 'Editing'
            );
            
            $extrainfo = array();
            if ($log->crud && isset($crudmap[$log->crud])) {
                $extrainfo[] = 'CRUD: ' . $crudmap[$log->crud];
            }
            if ($log->edulevel !== null && isset($edulevelmap[$log->edulevel])) {
                $extrainfo[] = 'Level: ' . $edulevelmap[$log->edulevel];
            }
            if ($log->objecttable) {
                $extrainfo[] = 'Table: ' . $log->objecttable;
            }
            
            $eventdisplay .= implode(' | ', $extrainfo);
            $eventdisplay .= '</small>';
        }
        
        $row[] = $eventdisplay;
        
        // Description - Always provide meaningful description
        $description = '';
        try {
            // Try to get event description
            if (class_exists($log->eventname)) {
                $eventclass = $log->eventname;
                
                // Prepare context safely
                $eventcontext = null;
                try {
                    $eventcontext = context::instance_by_id($log->contextid, IGNORE_MISSING);
                } catch (Exception $e) {
                    // Context might not exist, try to get system context
                    $eventcontext = context_system::instance();
                }
                
                if ($eventcontext) {
                    $eventdata = [
                        'objectid' => $log->objectid,
                        'context' => $eventcontext,
                        'userid' => $log->userid,
                        'relateduserid' => $log->relateduserid,
                        'other' => null
                    ];
                    
                    // Try to unserialize 'other' data
                    if ($log->other) {
                        try {
                            $eventdata['other'] = @unserialize($log->other);
                        } catch (Exception $e) {
                            // Keep other as null if unserialize fails
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
            // If we can't get description from event class, create a generic one
        }
        
        // If still no description, create a generic one based on available data
        if (empty($description)) {
            $description = 'The user';
            if ($log->firstname && $log->lastname) {
                $description .= " with id '{$log->userid}'";
            }
            
            // Add action information
            if ($log->action) {
                $description .= " {$log->action}";
            }
            
            // Add target information
            if ($log->target) {
                $description .= " the {$log->target}";
            }
            
            // Add object information
            if ($log->objectid) {
                $description .= " with id '{$log->objectid}'";
            }
            
            // Add course context if available
            if ($log->coursename) {
                $description .= " in the course '{$log->coursename}'";
            }
            
            $description .= '.';
        }
        
        $row[] = $description;
        
        // Origin
        $row[] = $log->origin ? $log->origin : '-';
        
        // IP Address
        $row[] = $log->ip ? $log->ip : '-';
        
        $table->data[] = $row;
    }
    
    echo html_writer::table($table);
    
    // Display count
    $count = count($logs);
    echo html_writer::tag('p', get_string('numberofentries', 'moodle', $count), array('class' => 'log-count'));
}
