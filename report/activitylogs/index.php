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

if ($data = $mform->get_data()) {
    // Process form data and display logs
    $userid = $data->userid;
    $courseid = $data->courseid;
    $datefrom = $data->datefrom;
    $dateto = $data->dateto;
    
    // Display the logs table
    display_logs_table($userid, $courseid, $datefrom, $dateto);
} else {
    echo html_writer::tag('p', get_string('selectcriteria', 'report_activitylogs'));
}

$mform->display();

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
    $sql = "SELECT l.id, l.timecreated, l.userid, l.eventname, l.component, l.action, 
                   l.target, l.objecttable, l.contextid, l.contextlevel, l.ip,
                   u.firstname, u.lastname, u.email,
                   c.fullname as coursename,
                   ctx.contextlevel, ctx.instanceid
            FROM {logstore_standard_log} l
            LEFT JOIN {user} u ON l.userid = u.id
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
        get_string('user', 'report_activitylogs'),
        get_string('event', 'report_activitylogs'),
        get_string('component', 'report_activitylogs'),
        get_string('context', 'report_activitylogs'),
        get_string('ipaddress', 'report_activitylogs')
    );
    $table->attributes['class'] = 'generaltable';
    
    foreach ($logs as $log) {
        $row = array();
        
        // Time
        $row[] = userdate($log->timecreated, get_string('strftimedatetime', 'langconfig'));
        
        // User
        if ($log->firstname && $log->lastname) {
            $userlink = html_writer::link(
                new moodle_url('/user/profile.php', array('id' => $log->userid)),
                fullname($log)
            );
            $row[] = $userlink;
        } else {
            $row[] = '-';
        }
        
        // Event name - make it more readable
        $eventname = str_replace('\\', ' ', $log->eventname);
        $eventname = preg_replace('/([a-z])([A-Z])/', '$1 $2', $eventname);
        $row[] = $eventname;
        
        // Component
        $row[] = $log->component ? $log->component : '-';
        
        // Context
        if ($log->coursename) {
            $contexttext = $log->coursename;
        } else {
            $contexttext = 'Context Level ' . $log->contextlevel;
        }
        $row[] = $contexttext;
        
        // IP Address
        $row[] = $log->ip ? $log->ip : '-';
        
        $table->data[] = $row;
    }
    
    echo html_writer::table($table);
    
    // Display count
    $count = count($logs);
    echo html_writer::tag('p', get_string('numberofentries', 'moodle', $count), array('class' => 'log-count'));
}
