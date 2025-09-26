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
 * Manage batches page for Test Event API plugin.
 *
 * @package    local_testeventapi
 * @copyright  2025 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/formslib.php');

// Check login and capabilities.
require_login();
require_capability('local/testeventapi:manage', context_system::instance());

$id = optional_param('id', 0, PARAM_INT); // Batch ID for editing.

$PAGE->set_url(new moodle_url('/local/testeventapi/manage.php', ['id' => $id]));
$PAGE->set_context(context_system::instance());

$batch = null;
if ($id) {
    $batch = \local_testeventapi\batch_manager::get_batch($id);
    if (!$batch) {
        throw new moodle_exception('batchnotfound', 'local_testeventapi');
    }
    $PAGE->set_title(get_string('editbatch', 'local_testeventapi'));
    $PAGE->set_heading(get_string('editbatch', 'local_testeventapi'));
} else {
    $PAGE->set_title(get_string('addbatch', 'local_testeventapi'));
    $PAGE->set_heading(get_string('addbatch', 'local_testeventapi'));
}

/**
 * Form for creating/editing batches.
 */
class batch_form extends moodleform {
    
    public function definition() {
        $mform = $this->_form;
        
        // Batch name.
        $mform->addElement('text', 'name', get_string('batchname', 'local_testeventapi'), 
            ['placeholder' => get_string('batchname_placeholder', 'local_testeventapi')]);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', get_string('batchname_required', 'local_testeventapi'), 'required');
        $mform->addHelpButton('name', 'batchname', 'local_testeventapi');
        
        // Start date.
        $mform->addElement('date_time_selector', 'start_date', get_string('startdate', 'local_testeventapi'));
        $mform->addRule('start_date', get_string('startdate_required', 'local_testeventapi'), 'required');
        $mform->addHelpButton('start_date', 'startdate', 'local_testeventapi');
        
        // Preview matching courses (for new batches).
        if (!$this->_customdata['id']) {
            $mform->addElement('static', 'preview_info', '', 
                '<div id="course-preview" class="alert alert-info">Chọn ngày bắt đầu để xem preview các môn học sẽ được thêm tự động.</div>');
        }
        
        // Hidden ID field for editing.
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        
        // Action buttons.
        $this->add_action_buttons();
    }
    
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        
        // Validate batch name length.
        if (strlen($data['name']) > 255) {
            $errors['name'] = get_string('batchname_toolong', 'local_testeventapi');
        }
        
        return $errors;
    }
}

// Initialize form.
$form = new batch_form(null, ['id' => $id]);

// Set default data for editing.
if ($batch) {
    $form->set_data([
        'id' => $batch->id,
        'name' => $batch->name,
        'start_date' => $batch->start_date,
    ]);
}

// Handle form submission.
if ($form->is_cancelled()) {
    redirect(new moodle_url('/local/testeventapi/index.php'));
} else if ($data = $form->get_data()) {
    try {
        if ($data->id) {
            // Update existing batch.
            $success = \local_testeventapi\batch_manager::update_batch($data->id, $data->name, $data->start_date);
            if ($success) {
                redirect(new moodle_url('/local/testeventapi/view.php', ['id' => $data->id]), 
                    get_string('batchupdated', 'local_testeventapi'), null, \core\output\notification::NOTIFY_SUCCESS);
            } else {
                $errormsg = get_string('batchupdatefailed', 'local_testeventapi');
            }
        } else {
            // Create new batch.
            $newid = \local_testeventapi\batch_manager::create_batch($data->name, $data->start_date);
            if ($newid) {
                redirect(new moodle_url('/local/testeventapi/view.php', ['id' => $newid]), 
                    get_string('batchcreated', 'local_testeventapi'), null, \core\output\notification::NOTIFY_SUCCESS);
            } else {
                $errormsg = get_string('batchcreatefailed', 'local_testeventapi');
            }
        }
    } catch (Exception $e) {
        $errormsg = $e->getMessage();
    }
}

echo $OUTPUT->header();

// Show error message if any.
if (isset($errormsg)) {
    echo $OUTPUT->notification($errormsg, 'error');
}

// Back button.
$backurl = new moodle_url('/local/testeventapi/index.php');
echo html_writer::div(
    html_writer::link($backurl, get_string('back', 'local_testeventapi'), ['class' => 'btn btn-secondary']),
    'mb-3'
);

// Display form.
$form->display();

// Add JavaScript for course preview (for new batches).
if (!$id) {
    $PAGE->requires->js_call_amd('local_testeventapi/course_preview', 'init');
}

echo $OUTPUT->footer();