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
 * Manage batch page for local_course_batches plugin
 *
 * @package    local_course_batches
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->libdir . '/formslib.php');

use local_course_batches\batch_manager;

/**
 * Form class để thêm/sửa đợt mở môn
 */
class batch_form extends moodleform {
    
    public function definition() {
        $mform = $this->_form;
        
        // Trường tên đợt mở môn
        $mform->addElement('text', 'batch_name', get_string('batch_name', 'local_course_batches'), 
                          array('size' => 50));
        $mform->setType('batch_name', PARAM_TEXT);
        $mform->addRule('batch_name', null, 'required', null, 'client');
        $mform->addRule('batch_name', null, 'maxlength', 255, 'client');
        
        // Trường ngày bắt đầu học
        $mform->addElement('date_selector', 'start_date', get_string('start_date', 'local_course_batches'));
        $mform->addRule('start_date', null, 'required', null, 'client');
        $mform->addHelpButton('start_date', 'start_date', 'local_course_batches');
        
        // Thông tin giải thích
        $mform->addElement('static', 'info', '', 'Các môn học có cùng ngày bắt đầu sẽ được tự động gán vào đợt này.');
        
        // Hidden field cho ID (khi edit)
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        
        // Hidden field cho action
        $mform->addElement('hidden', 'action');
        $mform->setType('action', PARAM_ALPHA);
        
        // Buttons
        $this->add_action_buttons(true, get_string('savechanges'));
    }
    
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        
        // Kiểm tra trùng lặp ngày bắt đầu (trừ record hiện tại khi edit)
        global $DB;
        if (!empty($data['id'])) {
            $sql = "SELECT id FROM {local_course_batches} 
                    WHERE start_date = ? AND id != ?";
            $exists = $DB->record_exists_sql($sql, array($data['start_date'], $data['id']));
        } else {
            $conditions = array('start_date' => $data['start_date']);
            $exists = $DB->record_exists('local_course_batches', $conditions);
        }
        
        if ($exists) {
            $errors['start_date'] = 'Đã có đợt mở môn với ngày bắt đầu này';
        }
        
        return $errors;
    }
}

// Kiểm tra đăng nhập và quyền truy cập
require_login();
$context = context_system::instance();
require_capability('local/course_batches:manage', $context);

// Lấy tham số
$action = required_param('action', PARAM_ALPHA);
$id = optional_param('id', 0, PARAM_INT);

// Thiết lập trang
$PAGE->set_url('/local/course_batches/manage.php', array('action' => $action, 'id' => $id));
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');

// Xử lý theo action
$batch = null;
if ($action == 'edit' && $id) {
    $batch = batch_manager::get_batch($id);
    if (!$batch) {
        throw new moodle_exception('Không tìm thấy đợt mở môn');
    }
    $PAGE->set_title(get_string('edit_batch', 'local_course_batches'));
    $PAGE->set_heading(get_string('edit_batch', 'local_course_batches'));
} else {
    $PAGE->set_title(get_string('add_batch', 'local_course_batches'));
    $PAGE->set_heading(get_string('add_batch', 'local_course_batches'));
}

// Tạo form
$mform = new batch_form();

// Thiết lập dữ liệu mặc định
if ($batch) {
    $mform->set_data(array(
        'id' => $batch->id,
        'action' => $action,
        'batch_name' => $batch->batch_name,
        'start_date' => $batch->start_date
    ));
} else {
    $mform->set_data(array('action' => $action));
}

// Xử lý submit form
if ($mform->is_cancelled()) {
    // Quay lại trang chính
    redirect(new moodle_url('/local/course_batches/index.php'));
} else if ($data = $mform->get_data()) {
    
    if ($action == 'edit' && $data->id) {
        // Cập nhật đợt mở môn
        batch_manager::update_batch($data->id, $data->batch_name, $data->start_date);
        $message = get_string('batch_updated', 'local_course_batches');
    } else {
        // Tạo đợt mở môn mới
        batch_manager::create_batch($data->batch_name, $data->start_date);
        $message = get_string('batch_created', 'local_course_batches');
    }
    
    redirect(new moodle_url('/local/course_batches/index.php'), $message, 
            null, \core\output\notification::NOTIFY_SUCCESS);
}

// Hiển thị trang
echo $OUTPUT->header();

// Nút quay lại
$back_url = new moodle_url('/local/course_batches/index.php');
echo html_writer::link($back_url, '← ' . get_string('back_to_batches', 'local_course_batches'), 
                     array('class' => 'btn btn-secondary mb-3'));

// Hiển thị form
$mform->display();

echo $OUTPUT->footer();