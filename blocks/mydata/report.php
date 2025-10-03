<?php
require_once('../../config.php');
require_once($CFG->dirroot . '/blocks/mydata/lib.php');

defined('MOODLE_INTERNAL') || die();

// Require login
require_login();

// Set up the page
$PAGE->set_url('/blocks/mydata/report.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('report_title', 'block_mydata'));
$PAGE->set_heading(get_string('report_heading', 'block_mydata'));
$PAGE->set_pagelayout('standard');

// Kiểm tra quyền Admin hoặc Manager
if (!block_mydata_has_access_permission()) {
    print_error('nopermissions', 'error', '', get_string('no_permission_error', 'block_mydata'));
}

echo $OUTPUT->header();

// Use form from classes/form
use block_mydata\form\course_selection_form;

// Create and display form
$mform = new course_selection_form();

// Process form submission
if ($fromform = $mform->get_data()) {
    if (!empty($fromform->courseids)) {
        // Get selected courses information
        list($insql, $params) = $DB->get_in_or_equal($fromform->courseids);
        $selected_courses = $DB->get_records_sql("
            SELECT id, fullname, shortname 
            FROM {course} 
            WHERE id $insql 
            ORDER BY fullname ASC
        ", $params);
        
        $templatedata = array();
        $templatedata['hasresults'] = true;
        $templatedata['courses'] = array();
        $templatedata['reporturl'] = new moodle_url('/blocks/mydata/report.php');
        
        foreach ($selected_courses as $course) {
            // Get users list from lib.php
            $users = block_mydata_get_course_users($course->id);
            
            $coursedata = array();
            $coursedata['coursename'] = $course->fullname;
            $coursedata['hasusers'] = !empty($users);
            
            if (!empty($users)) {
                $coursedata['users'] = $users;
                $coursedata['totalusers'] = count($users);
            }
            
            $templatedata['courses'][] = $coursedata;
        }
        
        echo $OUTPUT->render_from_template('block_mydata/course_selection', $templatedata);
    }
} else {
    // Display form with template
    $templatedata = array();
    $templatedata['hasresults'] = false;
    
    // Render form content
    ob_start();
    $mform->display();
    $formhtml = ob_get_clean();
    
    echo $OUTPUT->render_from_template('block_mydata/course_selection', $templatedata);
    echo $formhtml;
}

echo $OUTPUT->footer();