<?php
require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/local/createtable/lib.php');
require_once($CFG->dirroot . '/local/createtable/classes/batch_manager.php');
require_once($CFG->dirroot . '/local/createtable/classes/output/renderer.php');

// Require login and check capabilities
require_login();
$context = context_system::instance();
require_capability('moodle/site:config', $context);

// Get parameters
$id = optional_param('id', 0, PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);

// Set up the page
$PAGE->set_url('/local/createtable/manage.php', ['id' => $id]);
$PAGE->set_context($context);
$PAGE->set_title(get_string('pluginname', 'local_createtable'));
$PAGE->set_heading(get_string('pluginname', 'local_createtable'));

// Include CSS.
$PAGE->requires->css('/local/createtable/styles/styles.css');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && confirm_sesskey()) {
    $name = required_param('name', PARAM_TEXT);
    $opendate_str = required_param('opendate', PARAM_RAW);
    $opendate = strtotime($opendate_str);
    
    if ($id > 0) {
        // Update existing batch
        if (local_createtable\batch_manager::update_batch($id, $name, $opendate)) {
            redirect(new moodle_url('/local/createtable/index.php'), 
                get_string('batchupdated', 'local_createtable'), null, \core\output\notification::NOTIFY_SUCCESS);
        } else {
            redirect(new moodle_url('/local/createtable/manage.php', ['id' => $id]), 
                get_string('batchupdatefailed', 'local_createtable'), null, \core\output\notification::NOTIFY_ERROR);
        }
    } else {
        // Create new batch
        $newid = local_createtable\batch_manager::create_batch($name, $opendate);
        if ($newid) {
            redirect(new moodle_url('/local/createtable/index.php'), 
                get_string('batchcreated', 'local_createtable'), null, \core\output\notification::NOTIFY_SUCCESS);
        } else {
            redirect(new moodle_url('/local/createtable/manage.php'), 
                get_string('batchcreatefailed', 'local_createtable'), null, \core\output\notification::NOTIFY_ERROR);
        }
    }
}

// Handle delete action
if ($action === 'delete' && $id > 0 && confirm_sesskey()) {
    if (local_createtable\batch_manager::delete_batch($id)) {
        redirect(new moodle_url('/local/createtable/index.php'), 
            get_string('batchdeleted', 'local_createtable'), null, \core\output\notification::NOTIFY_SUCCESS);
    } else {
        redirect(new moodle_url('/local/createtable/index.php'), 
            get_string('batchdeletefailed', 'local_createtable'), null, \core\output\notification::NOTIFY_ERROR);
    }
}

echo $OUTPUT->header();

// Get batch data for editing
$batch = null;
if ($id > 0) {
    $batch = local_createtable\batch_manager::get_batch($id);
    if (!$batch) {
        print_error('batchnotfound', 'local_createtable');
    }
}

// Get template data and render form
$templatedata = \local_createtable\output\renderer::get_batch_form_data($batch);
echo $OUTPUT->render_from_template('local_createtable/batch_form', $templatedata);

echo $OUTPUT->footer();