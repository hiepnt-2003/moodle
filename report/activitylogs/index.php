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
require_once($CFG->dirroot.'/report/activitylogs/lib.php');

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
    // Process form data based on filter type
    $filtertype = $data->filtertype;
    
    $userids = array();
    $courseids = array();
    
    if ($filtertype === 'user') {
        // Filter by users - get selected user IDs
        $userids = isset($data->userids) ? $data->userids : array();
        // No course filter when filtering by user
    } else {
        // Filter by courses - get selected course IDs
        $courseids = isset($data->courseids) ? $data->courseids : array();
        // No user filter when filtering by course
    }
    
    $datefrom = $data->datefrom;
    $dateto = $data->dateto;
    
    // Display the logs table below form with selected users/courses
    report_activitylogs_display_logs_table($userids, $courseids, $datefrom, $dateto);
} else {
    echo html_writer::tag('p', get_string('selectcriteria', 'report_activitylogs'), array('class' => 'alert alert-info'));
}

echo $OUTPUT->footer();
