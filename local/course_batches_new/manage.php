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
 * Manage batches for local_course_batches plugin
 *
 * @package    local_course_batches
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->libdir . '/formslib.php');

use local_course_batches\batch_manager;

// Kiểm tra đăng nhập
require_login();

// Kiểm tra quyền truy cập
$context = context_system::instance();
require_capability('local/course_batches:manage', $context);

// Lấy tham số
$action = optional_param('action', 'list', PARAM_ALPHA);
$id = optional_param('id', 0, PARAM_INT);

// Thiết lập trang
$PAGE->set_url('/local/course_batches/manage.php', array('action' => $action, 'id' => $id));
$PAGE->set_context($context);
$PAGE->set_title(get_string('manage_batches', 'local_course_batches'));
$PAGE->set_heading(get_string('manage_batches', 'local_course_batches'));
$PAGE->set_pagelayout('admin');

/**
 * Form class for batch management
 */
class batch_form extends moodleform {
    
    public function definition() {
        $mform = $this->_form;
        
        // Hidden fields
        $mform->addElement('hidden', 'action');
        $mform->setType('action', PARAM_ALPHA);
        
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        
        // Batch name
        $mform->addElement('text', 'batch_name', get_string('batch_name', 'local_course_batches'), 
                          array('size' => '50', 'maxlength' => '255'));
        $mform->setType('batch_name', PARAM_TEXT);
        $mform->addRule('batch_name', get_string('required'), 'required', null, 'client');
        $mform->addHelpButton('batch_name', 'batch_name_help', 'local_course_batches');
        
        // Date picker for start date (optional for filtering courses)
        $mform->addElement('date_selector', 'start_date', get_string('start_date', 'local_course_batches'), 
                          array('optional' => true));
        $mform->addHelpButton('start_date', 'start_date_help', 'local_course_batches');
        
        // Auto assignment checkbox
        $mform->addElement('advcheckbox', 'auto_assign', get_string('auto_assign_courses', 'local_course_batches'),
                          get_string('auto_assign_courses_desc', 'local_course_batches'));
        $mform->setDefault('auto_assign', 1);
        $mform->addHelpButton('auto_assign', 'auto_assign_help', 'local_course_batches');
        
        // Submit buttons
        $this->add_action_buttons(true, get_string('save_batch', 'local_course_batches'));
    }
    
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        
        // Kiểm tra tên đợt không trùng
        if (!empty($data['batch_name'])) {
            global $DB;
            $conditions = array('batch_name' => $data['batch_name']);
            if (!empty($data['id'])) {
                $conditions['id'] = $data['id'];
                $existing = $DB->get_record('local_course_batches', $conditions);
                if (!$existing) {
                    // Kiểm tra tên khác không trùng
                    $existing_other = $DB->get_record('local_course_batches', 
                                                    array('batch_name' => $data['batch_name']));
                    if ($existing_other && $existing_other->id != $data['id']) {
                        $errors['batch_name'] = get_string('batch_name_exists', 'local_course_batches');
                    }
                }
            } else {
                $existing = $DB->get_record('local_course_batches', $conditions);
                if ($existing) {
                    $errors['batch_name'] = get_string('batch_name_exists', 'local_course_batches');
                }
            }
        }
        
        return $errors;
    }
}

// Xử lý action
$batch = null;
if ($action == 'edit' && $id) {
    $batch = batch_manager::get_batch($id);
    if (!$batch) {
        print_error('Batch not found');
    }
}

// Tạo form
$customdata = array('action' => $action, 'id' => $id);
$mform = new batch_form(null, $customdata);

// Thiết lập dữ liệu mặc định cho form
if ($batch) {
    $formdata = array(
        'action' => $action,
        'id' => $id,
        'batch_name' => $batch->batch_name,
        'start_date' => 0, // Mặc định không có ngày bắt đầu cụ thể
        'auto_assign' => 1
    );
    $mform->set_data($formdata);
} else {
    $mform->set_data(array(
        'action' => $action,
        'id' => $id,
        'auto_assign' => 1
    ));
}

// Xử lý submit form
if ($mform->is_cancelled()) {
    redirect(new moodle_url('/local/course_batches/index.php'));
} else if ($data = $mform->get_data()) {
    
    if ($action == 'edit' && $id) {
        // Cập nhật đợt
        $result = batch_manager::update_batch($id, $data->batch_name);
        if ($result) {
            // Nếu có yêu cầu auto assign
            if (!empty($data->auto_assign)) {
                if (!empty($data->start_date)) {
                    // Tự động gán theo ngày bắt đầu cụ thể
                    batch_manager::auto_assign_courses_by_start_date($id, $data->start_date);
                } else {
                    // Tự động gán tất cả khóa học có startdate
                    batch_manager::auto_assign_all_courses_with_startdate($id);
                }
            }
            
            redirect(new moodle_url('/local/course_batches/index.php'), 
                    get_string('batch_updated', 'local_course_batches'), 
                    null, \core\output\notification::NOTIFY_SUCCESS);
        } else {
            redirect(new moodle_url('/local/course_batches/manage.php', array('action' => 'edit', 'id' => $id)), 
                    get_string('batch_update_failed', 'local_course_batches'), 
                    null, \core\output\notification::NOTIFY_ERROR);
        }
    } else {
        // Tạo mới đợt
        $batch_id = batch_manager::create_batch($data->batch_name);
        if ($batch_id) {
            // Nếu có yêu cầu auto assign
            if (!empty($data->auto_assign)) {
                if (!empty($data->start_date)) {
                    // Tự động gán theo ngày bắt đầu cụ thể
                    batch_manager::auto_assign_courses_by_start_date($batch_id, $data->start_date);
                } else {
                    // Tự động gán tất cả khóa học có startdate
                    batch_manager::auto_assign_all_courses_with_startdate($batch_id);
                }
            }
            
            redirect(new moodle_url('/local/course_batches/index.php'), 
                    get_string('batch_created', 'local_course_batches'), 
                    null, \core\output\notification::NOTIFY_SUCCESS);
        } else {
            redirect(new moodle_url('/local/course_batches/manage.php', array('action' => 'add')), 
                    get_string('batch_create_failed', 'local_course_batches'), 
                    null, \core\output\notification::NOTIFY_ERROR);
        }
    }
}

// Hiển thị trang
echo $OUTPUT->header();

// Breadcrumb
$breadcrumbs = array();
$breadcrumbs[] = html_writer::link(new moodle_url('/local/course_batches/index.php'), 
                                  get_string('pluginname', 'local_course_batches'));

if ($action == 'edit') {
    echo $OUTPUT->heading(get_string('edit_batch', 'local_course_batches'));
    $breadcrumbs[] = get_string('edit_batch', 'local_course_batches');
} else {
    echo $OUTPUT->heading(get_string('add_batch', 'local_course_batches'));
    $breadcrumbs[] = get_string('add_batch', 'local_course_batches');
}

// Hiển thị breadcrumb
echo html_writer::start_div('breadcrumb-nav mb-3');
echo implode(' / ', $breadcrumbs);
echo html_writer::end_div();

// Hiển thị form
$mform->display();

// Hiển thị thông tin hướng dẫn
echo html_writer::start_div('alert alert-info mt-4');
echo html_writer::tag('h5', get_string('batch_help_title', 'local_course_batches'));
echo html_writer::tag('p', get_string('batch_help_content', 'local_course_batches'));
echo html_writer::start_tag('ul');
echo html_writer::tag('li', get_string('batch_help_item1', 'local_course_batches'));
echo html_writer::tag('li', get_string('batch_help_item2', 'local_course_batches'));
echo html_writer::tag('li', get_string('batch_help_item3', 'local_course_batches'));
echo html_writer::end_tag('ul');
echo html_writer::end_div();

echo $OUTPUT->footer();