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
 * User Activity Log report main page.
 *
 * @package    report_useractivitylog
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/report/useractivitylog/classes/form/filter_form.php');

// Check user is logged in and has permission
require_login();
$context = context_system::instance();
require_capability('report/useractivitylog:view', $context);

// Page setup
$PAGE->set_context($context);
$PAGE->set_url('/report/useractivitylog/index.php');
$PAGE->set_title(get_string('reporttitle', 'report_useractivitylog'));
$PAGE->set_heading(get_string('reporttitle', 'report_useractivitylog'));
$PAGE->navbar->add(get_string('reports'), new moodle_url('/admin/reports.php'));
$PAGE->navbar->add(get_string('pluginname', 'report_useractivitylog'));

// Create form
$mform = new \report_useractivitylog\form\filter_form();

// Handle form submission
$data = $mform->get_data();
$showresults = false;
$logs = array();

if ($data) {
    $showresults = true;
    $logs = get_user_activity_logs($data);
}

// Start output
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pluginname', 'report_useractivitylog'));

// Display form
$mform->display();

// Display results if form submitted
if ($showresults) {
    if (empty($logs)) {
        echo $OUTPUT->notification(get_string('nodata', 'report_useractivitylog'), 'info');
    } else {
        echo display_activity_table($logs, $data);
    }
}

echo $OUTPUT->footer();

/**
 * Get user activity logs based on filter criteria.
 *
 * @param object $data Form data
 * @return array
 */
function get_user_activity_logs($data) {
    global $DB;

    $params = array();
    $whereclause = array();

    // User filter
    if (!empty($data->userid)) {
        $whereclause[] = "l.userid = :userid";
        $params['userid'] = $data->userid;
    }

    // Date range filter
    if (!empty($data->startdate)) {
        $whereclause[] = "l.timecreated >= :startdate";
        $params['startdate'] = $data->startdate;
    }

    if (!empty($data->enddate)) {
        $whereclause[] = "l.timecreated <= :enddate";
        $params['enddate'] = $data->enddate + 86400; // End of day
    }

    // Course filter
    if (!empty($data->courseid)) {
        $whereclause[] = "l.courseid = :courseid";
        $params['courseid'] = $data->courseid;
    }

    $where = '';
    if (!empty($whereclause)) {
        $where = 'WHERE ' . implode(' AND ', $whereclause);
    }

    $sql = "SELECT l.id, l.timecreated, l.userid, l.relateduserid, l.contextid, 
                   l.component, l.action, l.target, l.objecttable, l.objectid, 
                   l.crud, l.edulevel, l.contextlevel, l.contextinstanceid, 
                   l.courseid, l.other, l.ip,
                   u.firstname, u.lastname, u.username,
                   ru.firstname as rel_firstname, ru.lastname as rel_lastname, ru.username as rel_username,
                   c.fullname as coursename, c.shortname as courseshortname
            FROM {logstore_standard_log} l
            LEFT JOIN {user} u ON l.userid = u.id
            LEFT JOIN {user} ru ON l.relateduserid = ru.id
            LEFT JOIN {course} c ON l.courseid = c.id
            $where
            ORDER BY l.timecreated DESC
            LIMIT 1000";

    return $DB->get_records_sql($sql, $params);
}

/**
 * Display activity logs in a table format.
 *
 * @param array $logs
 * @param object $data
 * @return string
 */
function display_activity_table($logs, $data) {
    global $OUTPUT;

    $table = new html_table();
    $table->head = array(
        get_string('time', 'report_useractivitylog'),
        get_string('fullname', 'report_useractivitylog'),
        get_string('affecteduser', 'report_useractivitylog'),
        get_string('eventcontext', 'report_useractivitylog'),
        get_string('component', 'report_useractivitylog'),
        get_string('eventname', 'report_useractivitylog'),
        get_string('description', 'report_useractivitylog'),
        get_string('origin', 'report_useractivitylog'),
        get_string('ipaddress', 'report_useractivitylog')
    );

    $table->attributes['class'] = 'generaltable';
    $table->data = array();

    foreach ($logs as $log) {
        $row = array();
        
        // Time
        $row[] = userdate($log->timecreated, get_string('strftimedatetimeshort', 'langconfig'));
        
        // User full name
        $fullname = fullname($log);
        if (!$fullname) {
            $fullname = $log->username ?: '-';
        }
        $row[] = $fullname;
        
        // Affected user
        if ($log->relateduserid) {
            $affected = '';
            if ($log->rel_firstname || $log->rel_lastname) {
                $relateduser = new stdClass();
                $relateduser->firstname = $log->rel_firstname;
                $relateduser->lastname = $log->rel_lastname;
                $affected = fullname($relateduser);
            }
            if (!$affected) {
                $affected = $log->rel_username ?: $log->relateduserid;
            }
            $row[] = $affected;
        } else {
            $row[] = '-';
        }
        
        // Event context
        $context = '';
        if ($log->courseid && $log->coursename) {
            $context = $log->coursename;
        } else if ($log->contextlevel) {
            $context = 'Context Level: ' . $log->contextlevel;
        }
        $row[] = $context;
        
        // Component
        $row[] = $log->component ?: 'System';
        
        // Event name
        $eventname = $log->action;
        if ($log->target) {
            $eventname .= ' ' . $log->target;
        }
        $row[] = $eventname;
        
        // Description
        $description = '';
        if ($log->other) {
            $other = @json_decode($log->other, true);
            if (is_array($other)) {
                $description = implode(', ', array_slice($other, 0, 3));
            }
        }
        if (!$description && $log->objecttable && $log->objectid) {
            $description = "Object: {$log->objecttable} (ID: {$log->objectid})";
        }
        $row[] = $description ?: '-';
        
        // Origin
        $origin = 'web';
        if (strpos($log->component, 'webservice') !== false) {
            $origin = 'webservice';
        } else if (strpos($log->component, 'cli') !== false) {
            $origin = 'cli';
        }
        $row[] = $origin;
        
        // IP Address
        $row[] = $log->ip ?: '-';
        
        $table->data[] = $row;
    }

    $output = html_writer::tag('h3', 'Activity Log Results (' . count($logs) . ' records)');
    $output .= html_writer::table($table);
    
    return $output;
}