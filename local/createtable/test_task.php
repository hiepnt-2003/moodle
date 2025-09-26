<?php
/**
 * Manual test script for monthly course creation task.
 * 
 * This script allows you to test the scheduled task manually
 * without waiting for the scheduled time.
 * 
 * Run this from command line:
 * php admin/cli/scheduled_task.php --execute=\\local_createtable\\task\\monthly_course_creation
 * 
 * Or access this file directly (for testing only):
 * /local/createtable/test_task.php
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

// Check user permissions - only allow for admin users.
require_login();
$context = context_system::instance();
require_capability('moodle/site:config', $context);

// Set up the page.
$PAGE->set_url('/local/createtable/test_task.php');
$PAGE->set_context($context);
$PAGE->set_title('Test Monthly Course Creation Task');
$PAGE->set_heading('Test Monthly Course Creation Task');

echo $OUTPUT->header();

echo $OUTPUT->heading('Manual Task Testing');

if (optional_param('run', 0, PARAM_INT)) {
    echo html_writer::div('Running monthly course creation task...', 'alert alert-info');
    
    try {
        $task = new \local_createtable\task\monthly_course_creation();
        
        // Capture output.
        ob_start();
        $task->execute();
        $output = ob_get_clean();
        
        echo html_writer::div('Task completed successfully!', 'alert alert-success');
        echo html_writer::tag('h4', 'Task Output:');
        echo html_writer::tag('pre', $output, ['class' => 'bg-light p-3']);
        
    } catch (Exception $e) {
        echo html_writer::div('Task failed: ' . $e->getMessage(), 'alert alert-danger');
        echo html_writer::tag('pre', $e->getTraceAsString(), ['class' => 'bg-light p-3']);
    }
    
    echo html_writer::link(
        new moodle_url('/local/createtable/test_task.php'),
        'Run Again',
        ['class' => 'btn btn-primary']
    );
    
} else {
    echo html_writer::div(
        'Click the button below to manually run the monthly course creation task. ' . 
        'This will create a batch for the current month if it doesn\'t exist.',
        'alert alert-info'
    );
    
    echo html_writer::link(
        new moodle_url('/local/createtable/test_task.php', ['run' => 1]),
        'Run Monthly Course Creation Task',
        ['class' => 'btn btn-primary btn-lg']
    );
}

echo html_writer::div('', 'mt-4');
echo html_writer::link(
    new moodle_url('/local/createtable/index.php'),
    'Back to Create Table Manager',
    ['class' => 'btn btn-secondary']
);

echo $OUTPUT->footer();
?>