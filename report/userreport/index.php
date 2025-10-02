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
 * Main index file for User Activity Report plugin.
 *
 * @package    report_userreport
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/report/userreport/classes/form/report_form.php');

// Check login and permissions.
require_login();
require_capability('report/userreport:view', context_system::instance());

// Set up the page.
$PAGE->set_url('/report/userreport/index.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('reporttitle', 'report_userreport'));
$PAGE->set_heading(get_string('reporttitle', 'report_userreport'));
$PAGE->set_pagelayout('report');

// Add CSS.
$PAGE->requires->css('/report/userreport/styles.css');

// Navigation.
$PAGE->navbar->add(get_string('reports'), new moodle_url('/admin/reports.php'));
$PAGE->navbar->add(get_string('pluginname', 'report_userreport'));

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('reporttitle', 'report_userreport'));

// Create and process form.
$mform = new report_userreport_form();

if ($data = $mform->get_data()) {
    // Generate report.
    $userid = $data->userid;
    $startdate = $data->startdate;
    $enddate = $data->enddate;
    $courseid = $data->courseid;

    // Build SQL query.
    $sql = "SELECT l.id, l.timecreated, l.userid, l.relateduserid, l.contextid, 
                   l.component, l.eventname, l.description, l.origin, l.ip,
                   u.firstname, u.lastname, u.deleted as user_deleted,
                   ru.firstname as rel_firstname, ru.lastname as rel_lastname, ru.deleted as rel_deleted,
                   c.contextlevel, c.instanceid,
                   co.fullname as course_fullname
            FROM {logstore_standard_log} l
            LEFT JOIN {user} u ON l.userid = u.id
            LEFT JOIN {user} ru ON l.relateduserid = ru.id
            LEFT JOIN {context} c ON l.contextid = c.id
            LEFT JOIN {course} co ON (c.contextlevel = ? AND c.instanceid = co.id)
            WHERE l.timecreated >= ? AND l.timecreated <= ?";

    $params = [CONTEXT_COURSE, $startdate, $enddate];

    // Filter by user.
    if ($userid > 0) {
        $sql .= " AND (l.userid = ? OR l.relateduserid = ?)";
        $params[] = $userid;
        $params[] = $userid;
    }

    // Filter by course.
    if ($courseid > 0) {
        $sql .= " AND c.instanceid = ? AND c.contextlevel = ?";
        $params[] = $courseid;
        $params[] = CONTEXT_COURSE;
    }

    $sql .= " ORDER BY l.timecreated DESC LIMIT 1000";

    $logs = $DB->get_records_sql($sql, $params);

    if (!empty($logs)) {
        // Display results in a table.
        echo html_writer::start_tag('div', array('class' => 'table-responsive'));
        echo html_writer::start_tag('table', array('class' => 'table table-striped report-userreport-table'));
        
        // Table header.
        echo html_writer::start_tag('thead');
        echo html_writer::start_tag('tr');
        echo html_writer::tag('th', get_string('time', 'report_userreport'));
        echo html_writer::tag('th', get_string('userfullname', 'report_userreport'));
        echo html_writer::tag('th', get_string('affecteduser', 'report_userreport'));
        echo html_writer::tag('th', get_string('eventcontext', 'report_userreport'));
        echo html_writer::tag('th', get_string('component', 'report_userreport'));
        echo html_writer::tag('th', get_string('eventname', 'report_userreport'));
        echo html_writer::tag('th', get_string('description', 'report_userreport'));
        echo html_writer::tag('th', get_string('origin', 'report_userreport'));
        echo html_writer::tag('th', get_string('ipaddress', 'report_userreport'));
        echo html_writer::end_tag('tr');
        echo html_writer::end_tag('thead');

        // Table body.
        echo html_writer::start_tag('tbody');
        foreach ($logs as $log) {
            echo html_writer::start_tag('tr');
            
            // Time.
            echo html_writer::tag('td', userdate($log->timecreated, '%d %B %Y, %I:%M:%S %p'));
            
            // User full name.
            if ($log->user_deleted) {
                $username = get_string('userdeleted', 'report_userreport');
            } else {
                $username = fullname($log);
            }
            echo html_writer::tag('td', $username);
            
            // Affected user.
            if ($log->relateduserid) {
                if ($log->rel_deleted) {
                    $affecteduser = get_string('userdeleted', 'report_userreport');
                } else {
                    $affecteduser = $log->rel_firstname . ' ' . $log->rel_lastname;
                }
            } else {
                $affecteduser = '-';
            }
            echo html_writer::tag('td', $affecteduser);
            
            // Event context.
            $context_display = '';
            if ($log->course_fullname) {
                $context_display = $log->course_fullname;
            } else {
                $context_display = 'System';
            }
            echo html_writer::tag('td', $context_display);
            
            // Component.
            echo html_writer::tag('td', $log->component ?: 'System');
            
            // Event name.
            $event_display = str_replace('\\', ' ', $log->eventname);
            echo html_writer::tag('td', $event_display);
            
            // Description.
            echo html_writer::tag('td', format_text($log->description, FORMAT_PLAIN));
            
            // Origin.
            echo html_writer::tag('td', $log->origin);
            
            // IP address.
            echo html_writer::tag('td', $log->ip);
            
            echo html_writer::end_tag('tr');
        }
        echo html_writer::end_tag('tbody');
        echo html_writer::end_tag('table');
        echo html_writer::end_tag('div');
    } else {
        echo html_writer::div(get_string('noresults', 'report_userreport'), 'alert alert-info');
    }
}

// Display the form.
echo html_writer::start_tag('div', array('class' => 'report-userreport-form'));
$mform->display();
echo html_writer::end_tag('div');

echo $OUTPUT->footer();