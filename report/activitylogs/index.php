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

// Get page parameter for pagination
$page = optional_param('page', 0, PARAM_INT);

// Get filter parameters from URL (to maintain filters when changing pages)
$filtertype = optional_param('filtertype', '', PARAM_ALPHA);
$userids = optional_param_array('userids', array(), PARAM_INT);
$courseids = optional_param_array('courseids', array(), PARAM_INT);
$datefrom = optional_param('datefrom', 0, PARAM_INT);
$dateto = optional_param('dateto', 0, PARAM_INT);

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

// If form is submitted, get data from form. Otherwise, use URL parameters (for pagination)
if ($data) {
    // Form was just submitted, reset to page 0
    $page = 0;
} else if ($filtertype) {
    // Coming from pagination, reconstruct data from URL parameters
    $data = new stdClass();
    $data->filtertype = $filtertype;
    $data->userids = $userids;
    $data->courseids = $courseids;
    $data->datefrom = $datefrom;
    $data->dateto = $dateto;
}

// Display form first
$mform->display();

// Then display results below the form
if ($data) {
    // Process form data based on filter type
    $filtertype = $data->filtertype;
    
    $userids_filter = array();
    $courseids_filter = array();
    
    if ($filtertype === 'user') {
        // Filter by users - get selected user IDs
        $userids_filter = isset($data->userids) ? $data->userids : array();
        // No course filter when filtering by user
    } else {
        // Filter by courses - get selected course IDs
        $courseids_filter = isset($data->courseids) ? $data->courseids : array();
        // No user filter when filtering by course
    }
    
    $datefrom_filter = $data->datefrom;
    $dateto_filter = $data->dateto;
    
    // Display the logs table below form with pagination
    report_activitylogs_display_logs_table($userids_filter, $courseids_filter, $datefrom_filter, $dateto_filter, $page);
} else {
    echo html_writer::tag('p', get_string('selectcriteria', 'report_activitylogs'), array('class' => 'alert alert-info'));
}

echo $OUTPUT->footer();
