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
 * Logs report main page.
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
$PAGE->set_pagelayout('report');
$PAGE->navbar->add(get_string('reports'), new moodle_url('/admin/reports.php'));
$PAGE->navbar->add(get_string('pluginname', 'report_useractivitylog'));

// Create form
$mform = new \report_useractivitylog\form\filter_form();

// Handle form submission
$data = $mform->get_data();
$showresults = false;
$logs = array();
$totalcount = 0;

if ($data) {
    $showresults = true;
    $logs = get_logs($data);
    $totalcount = count_logs($data);
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
        echo display_logs_table($logs, $data, $totalcount);
    }
}

echo $OUTPUT->footer();

/**
 * Get logs based on filter criteria.
 *
 * @param object $data Form data
 * @return array
 */
function get_logs($data) {
    global $DB;

    $params = array();
    $whereclause = array();

    // User filter
    if (!empty($data->userid) && $data->userid > 0) {
        $whereclause[] = "l.userid = :userid";
        $params['userid'] = $data->userid;
    }

    // Date filter (single date, like standard logs)
    if (!empty($data->date)) {
        $startdate = $data->date;
        $enddate = $data->date + 86400; // End of day
        $whereclause[] = "l.timecreated >= :startdate AND l.timecreated < :enddate";
        $params['startdate'] = $startdate;
        $params['enddate'] = $enddate;
    }

    // Course filter
    if (!empty($data->courseid) && $data->courseid > 0) {
        $whereclause[] = "l.courseid = :courseid";
        $params['courseid'] = $data->courseid;
    }

    // Module/activity filter
    if (!empty($data->modid)) {
        $whereclause[] = "l.component LIKE :modid";
        $params['modid'] = 'mod_' . $data->modid . '%';
    }

    // Action filter (CRUD)
    if (!empty($data->action)) {
        $whereclause[] = "l.crud = :action";
        $params['action'] = $data->action;
    }

    // Education level filter
    if (!empty($data->edulevel) && $data->edulevel >= 0) {
        $whereclause[] = "l.edulevel = :edulevel";
        $params['edulevel'] = $data->edulevel;
    }

    // Origin filter
    if (!empty($data->origin)) {
        switch ($data->origin) {
            case 'web':
                $whereclause[] = "l.origin = 'web'";
                break;
            case 'ws':
                $whereclause[] = "l.origin = 'ws'";
                break;
            case 'cli':
                $whereclause[] = "l.origin = 'cli'";
                break;
            case 'restore':
                $whereclause[] = "l.origin = 'restore'";
                break;
        }
    }

    $where = '';
    if (!empty($whereclause)) {
        $where = 'WHERE ' . implode(' AND ', $whereclause);
    }

    // Limit results
    $limit = !empty($data->logsperpage) ? (int)$data->logsperpage : 100;
    if ($limit > 5000) {
        $limit = 5000;
    }

    $sql = "SELECT l.id, l.timecreated, l.userid, l.relateduserid, l.contextid, 
                   l.component, l.action, l.target, l.objecttable, l.objectid, 
                   l.crud, l.edulevel, l.contextlevel, l.contextinstanceid, 
                   l.courseid, l.other, l.ip, l.origin,
                   u.firstname, u.lastname, u.username,
                   ru.firstname as rel_firstname, ru.lastname as rel_lastname, 
                   ru.username as rel_username,
                   c.fullname as coursename, c.shortname as courseshortname
            FROM {logstore_standard_log} l
            LEFT JOIN {user} u ON l.userid = u.id
            LEFT JOIN {user} ru ON l.relateduserid = ru.id
            LEFT JOIN {course} c ON l.courseid = c.id
            $where
            ORDER BY l.timecreated DESC
            LIMIT $limit";

    return $DB->get_records_sql($sql, $params);
}

/**
 * Count logs based on filter criteria.
 *
 * @param object $data Form data
 * @return int
 */
function count_logs($data) {
    global $DB;

    $params = array();
    $whereclause = array();

    // Same filtering logic as get_logs
    if (!empty($data->userid) && $data->userid > 0) {
        $whereclause[] = "l.userid = :userid";
        $params['userid'] = $data->userid;
    }

    if (!empty($data->date)) {
        $startdate = $data->date;
        $enddate = $data->date + 86400;
        $whereclause[] = "l.timecreated >= :startdate AND l.timecreated < :enddate";
        $params['startdate'] = $startdate;
        $params['enddate'] = $enddate;
    }

    if (!empty($data->courseid) && $data->courseid > 0) {
        $whereclause[] = "l.courseid = :courseid";
        $params['courseid'] = $data->courseid;
    }

    if (!empty($data->modid)) {
        $whereclause[] = "l.component LIKE :modid";
        $params['modid'] = 'mod_' . $data->modid . '%';
    }

    if (!empty($data->action)) {
        $whereclause[] = "l.crud = :action";
        $params['action'] = $data->action;
    }

    if (!empty($data->edulevel) && $data->edulevel >= 0) {
        $whereclause[] = "l.edulevel = :edulevel";
        $params['edulevel'] = $data->edulevel;
    }

    if (!empty($data->origin)) {
        switch ($data->origin) {
            case 'web':
                $whereclause[] = "l.origin = 'web'";
                break;
            case 'ws':
                $whereclause[] = "l.origin = 'ws'";
                break;
            case 'cli':
                $whereclause[] = "l.origin = 'cli'";
                break;
            case 'restore':
                $whereclause[] = "l.origin = 'restore'";
                break;
        }
    }

    $where = '';
    if (!empty($whereclause)) {
        $where = 'WHERE ' . implode(' AND ', $whereclause);
    }

    $sql = "SELECT COUNT(*) FROM {logstore_standard_log} l $where";

    return $DB->count_records_sql($sql, $params);
}

/**
 * Display logs in a table format like standard Moodle logs.
 *
 * @param array $logs
 * @param object $data
 * @param int $totalcount
 * @return string
 */
function display_logs_table($logs, $data, $totalcount) {
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

    $table->attributes['class'] = 'generaltable logtable';
    $table->data = array();

    foreach ($logs as $log) {
        $row = array();
        
        // Time - formatted like standard logs
        $row[] = userdate($log->timecreated, get_string('strftimedatetimeshort', 'langconfig'));
        
        // User full name with link (if possible)
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
        
        // Event context - shows course or system context
        $context = '';
        if ($log->courseid && $log->coursename) {
            $context = format_string($log->coursename);
        } else if ($log->contextlevel == CONTEXT_SYSTEM) {
            $context = get_string('coresystem');
        } else {
            $context = 'Context: ' . $log->contextlevel;
        }
        $row[] = $context;
        
        // Component - clean component name
        $component = $log->component;
        if (strpos($component, 'mod_') === 0) {
            $component = substr($component, 4); // Remove 'mod_' prefix
        }
        $row[] = $component ?: 'core';
        
        // Event name - combine action and target
        $eventname = $log->action;
        if ($log->target) {
            $eventname = ucfirst($log->action) . ' ' . $log->target;
        }
        $row[] = $eventname;
        
        // Description - try to get meaningful description
        $description = '';
        if ($log->other) {
            $other = @json_decode($log->other, true);
            if (is_array($other)) {
                // Show some key information from other data
                $desc_parts = array();
                foreach ($other as $key => $value) {
                    if (is_string($value) && strlen($value) < 50) {
                        $desc_parts[] = "$key: $value";
                    }
                    if (count($desc_parts) >= 2) break;
                }
                $description = implode(', ', $desc_parts);
            }
        }
        if (!$description && $log->objecttable && $log->objectid) {
            $description = "{$log->objecttable} (ID: {$log->objectid})";
        }
        $row[] = $description ?: '-';
        
        // Origin - standardized origin
        $origin = $log->origin ?: 'web';
        $origins_map = array(
            'web' => 'Web',
            'ws' => 'Web service',
            'cli' => 'Command line',
            'restore' => 'Restore'
        );
        $row[] = $origins_map[$origin] ?? ucfirst($origin);
        
        // IP Address
        $row[] = $log->ip ?: '-';
        
        $table->data[] = $row;
    }

    // Build output like standard logs
    $output = '';
    
    // Show total count
    if ($totalcount > count($logs)) {
        $a = new stdClass();
        $a->displayed = count($logs);
        $a->total = $totalcount;
        $output .= $OUTPUT->notification(get_string('logshowing', 'admin', $a), 'info');
    }
    
    // Display the table
    $output .= html_writer::table($table);
    
    return $output;
}