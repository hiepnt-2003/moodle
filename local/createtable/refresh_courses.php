<?php
/**
 * Refresh auto-courses for a batch
 *
 * This page handles refreshing the automatic course assignments
 * for a specific batch based on matching start dates.
 *
 * @package    local_createtable
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

// Check user permissions
require_login();
$context = context_system::instance();
require_capability('moodle/site:config', $context);

$batchid = required_param('id', PARAM_INT);

// Verify batch exists
$batch = $DB->get_record('local_createtable_batches', ['id' => $batchid], '*', MUST_EXIST);

// Set up the page
$PAGE->set_url('/local/createtable/refresh_courses.php', ['id' => $batchid]);
$PAGE->set_context($context);
$PAGE->set_title(get_string('refresh_courses', 'local_createtable'));
$PAGE->set_heading(get_string('refresh_courses', 'local_createtable'));

// Breadcrumb
$PAGE->navbar->add(get_string('pluginname', 'local_createtable'), new moodle_url('/local/createtable/index.php'));
$PAGE->navbar->add($batch->name, new moodle_url('/local/createtable/view.php', ['id' => $batchid]));
$PAGE->navbar->add(get_string('refresh_courses', 'local_createtable'));

// Process the refresh action
if (confirm_sesskey()) {
    try {
        $manager = new \local_createtable\batch_manager();
        $added_count = $manager->auto_add_courses_by_date($batchid);
        
        $message = '';
        if ($added_count > 0) {
            $message = get_string('coursesautoadded', 'local_createtable', $added_count);
            redirect(
                new moodle_url('/local/createtable/view.php', ['id' => $batchid]),
                $message,
                null,
                \core\output\notification::NOTIFY_SUCCESS
            );
        } else {
            $message = get_string('nocoursestoadd', 'local_createtable');
            redirect(
                new moodle_url('/local/createtable/view.php', ['id' => $batchid]),
                $message,
                null,
                \core\output\notification::NOTIFY_INFO
            );
        }
    } catch (Exception $e) {
        redirect(
            new moodle_url('/local/createtable/view.php', ['id' => $batchid]),
            get_string('errorrefreshingcourses', 'local_createtable') . ': ' . $e->getMessage(),
            null,
            \core\output\notification::NOTIFY_ERROR
        );
    }
} else {
    // Show confirmation page
    echo $OUTPUT->header();
    
    echo $OUTPUT->heading(get_string('refresh_courses', 'local_createtable'));
    
    // Get preview of courses that will be added
    $manager = new \local_createtable\batch_manager();
    $preview_courses = $manager->get_courses_by_date($batch->open_date);
    
    // Filter out already added courses
    $existing_sql = "SELECT courseid FROM {local_createtable_courses} WHERE batchid = ?";
    $existing_courses = $DB->get_records_sql($existing_sql, [$batchid]);
    $existing_ids = array_keys($existing_courses);
    
    $new_courses = [];
    foreach ($preview_courses as $course) {
        if (!in_array($course->id, $existing_ids)) {
            $new_courses[] = $course;
        }
    }
    
    if (!empty($new_courses)) {
        echo html_writer::div(
            get_string('confirmrefreshcourses', 'local_createtable', count($new_courses)),
            'alert alert-info'
        );
        
        // Show preview table
        $table = new html_table();
        $table->head = [
            get_string('coursename', 'local_createtable'),
            get_string('shortname', 'local_createtable'),
            get_string('startdate', 'local_createtable')
        ];
        
        foreach ($new_courses as $course) {
            $table->data[] = [
                format_string($course->fullname),
                format_string($course->shortname),
                userdate($course->startdate)
            ];
        }
        
        echo html_writer::table($table);
        
        // Confirmation buttons
        $continue_url = new moodle_url('/local/createtable/refresh_courses.php', [
            'id' => $batchid,
            'sesskey' => sesskey()
        ]);
        $cancel_url = new moodle_url('/local/createtable/view.php', ['id' => $batchid]);
        
        echo $OUTPUT->confirm(
            get_string('proceedrefresh', 'local_createtable'),
            $continue_url,
            $cancel_url
        );
        
    } else {
        echo html_writer::div(
            get_string('nocoursestoadd', 'local_createtable'),
            'alert alert-info'
        );
        
        $back_url = new moodle_url('/local/createtable/view.php', ['id' => $batchid]);
        echo $OUTPUT->single_button($back_url, get_string('back'), 'get');
    }
    
    echo $OUTPUT->footer();
}
?>