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
 * Manage courses in batch page for local_course_batches plugin
 *
 * @package    local_course_batches
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->libdir . '/tablelib.php');

use local_course_batches\batch_manager;

// Kiểm tra đăng nhập và quyền truy cập
require_login();
$context = context_system::instance();
require_capability('local/course_batches:manage', $context);

// Lấy tham số
$batch_id = required_param('batch_id', PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);
$course_id = optional_param('course_id', 0, PARAM_INT);

// Lấy thông tin đợt mở môn
$batch = batch_manager::get_batch($batch_id);
if (!$batch) {
    throw new moodle_exception('Không tìm thấy đợt mở môn');
}

// Thiết lập trang
$PAGE->set_url('/local/course_batches/manage_courses.php', array('batch_id' => $batch_id));
$PAGE->set_context($context);
$PAGE->set_title('Quản lý khóa học - ' . $batch->batch_name);
$PAGE->set_heading('Quản lý khóa học - ' . $batch->batch_name);
$PAGE->set_pagelayout('admin');

// Xử lý các action
if ($action && confirm_sesskey()) {
    switch ($action) {
        case 'add_course':
            if ($course_id) {
                batch_manager::add_course_to_batch($batch_id, $course_id);
                redirect($PAGE->url, 'Đã thêm khóa học vào đợt', null, \core\output\notification::NOTIFY_SUCCESS);
            }
            break;
            
        case 'remove_course':
            if ($course_id) {
                batch_manager::remove_course_from_batch($batch_id, $course_id);
                redirect($PAGE->url, 'Đã xóa khóa học khỏi đợt', null, \core\output\notification::NOTIFY_SUCCESS);
            }
            break;
            
        case 'auto_assign':
            $count = batch_manager::auto_assign_courses_to_batch($batch_id, $batch->start_date);
            redirect($PAGE->url, "Đã tự động gán {$count} khóa học vào đợt", null, \core\output\notification::NOTIFY_SUCCESS);
            break;
    }
}

// Hiển thị trang
echo $OUTPUT->header();

// Nút quay lại
$back_url = new moodle_url('/local/course_batches/index.php', array('action' => 'view_courses', 'id' => $batch_id));
echo html_writer::link($back_url, '← Quay lại xem đợt', array('class' => 'btn btn-secondary mb-3'));

// Thông tin đợt
echo html_writer::start_div('alert alert-info mb-4');
echo html_writer::tag('h5', 'Thông tin đợt mở môn');
echo 'Tên đợt: ' . $batch->batch_name . html_writer::empty_tag('br');
echo 'Ngày bắt đầu: ' . date('d/m/Y', $batch->start_date) . html_writer::empty_tag('br');
if (!empty($batch->description)) {
    echo 'Mô tả: ' . $batch->description;
}
echo html_writer::end_div();

// Nút tự động gán
$auto_assign_url = new moodle_url($PAGE->url, array('action' => 'auto_assign', 'sesskey' => sesskey()));
echo html_writer::link($auto_assign_url, 'Tự động gán khóa học cùng ngày bắt đầu', 
                     array('class' => 'btn btn-success mb-3'));

// Tabs
echo html_writer::start_tag('ul', array('class' => 'nav nav-tabs mb-3', 'role' => 'tablist'));

echo html_writer::start_tag('li', array('class' => 'nav-item', 'role' => 'presentation'));
echo html_writer::link('#assigned', 'Khóa học trong đợt', array(
    'class' => 'nav-link active',
    'data-bs-toggle' => 'tab',
    'role' => 'tab'
));
echo html_writer::end_tag('li');

echo html_writer::start_tag('li', array('class' => 'nav-item', 'role' => 'presentation'));
echo html_writer::link('#unassigned', 'Khóa học chưa gán', array(
    'class' => 'nav-link',
    'data-bs-toggle' => 'tab',
    'role' => 'tab'
));
echo html_writer::end_tag('li');

echo html_writer::end_tag('ul');

// Tab content
echo html_writer::start_div('tab-content');

// Tab 1: Khóa học trong đợt
echo html_writer::start_div('tab-pane fade show active', array('id' => 'assigned'));
echo html_writer::tag('h4', 'Khóa học trong đợt này');

$assigned_courses = batch_manager::get_courses_in_batch($batch_id);
if (empty($assigned_courses)) {
    echo $OUTPUT->notification('Chưa có khóa học nào trong đợt này.', 'info');
} else {
    $table = new html_table();
    $table->head = array('ID', 'Tên khóa học', 'Tên viết tắt', 'Ngày bắt đầu', 'Trạng thái', 'Thao tác');
    $table->attributes['class'] = 'table table-striped';
    
    foreach ($assigned_courses as $course) {
        $row = array();
        $row[] = $course->id;
        $row[] = html_writer::link(new moodle_url('/course/view.php', array('id' => $course->id)), 
                                 $course->fullname, array('target' => '_blank'));
        $row[] = $course->shortname;
        $row[] = date('d/m/Y', $course->startdate);
        $row[] = $course->visible ? '<span class="badge bg-success">Hiển thị</span>' : '<span class="badge bg-secondary">Ẩn</span>';
        
        // Nút xóa khỏi đợt
        $remove_url = new moodle_url($PAGE->url, array(
            'action' => 'remove_course',
            'course_id' => $course->id,
            'sesskey' => sesskey()
        ));
        $row[] = html_writer::link($remove_url, 'Xóa khỏi đợt', 
                                 array('class' => 'btn btn-sm btn-danger', 
                                       'onclick' => 'return confirm("Bạn có chắc muốn xóa khóa học này khỏi đợt?")'));
        
        $table->data[] = $row;
    }
    
    echo html_writer::table($table);
    echo html_writer::tag('p', 'Tổng cộng: ' . count($assigned_courses) . ' khóa học', 
                         array('class' => 'text-muted'));
}
echo html_writer::end_div();

// Tab 2: Khóa học chưa gán
echo html_writer::start_div('tab-pane fade', array('id' => 'unassigned'));
echo html_writer::tag('h4', 'Khóa học chưa được gán vào đợt nào');

$unassigned_courses = batch_manager::get_unassigned_courses();
if (empty($unassigned_courses)) {
    echo $OUTPUT->notification('Tất cả khóa học đã được gán vào các đợt.', 'success');
} else {
    $table = new html_table();
    $table->head = array('ID', 'Tên khóa học', 'Tên viết tắt', 'Ngày bắt đầu', 'Trạng thái', 'Thao tác');
    $table->attributes['class'] = 'table table-striped';
    
    foreach ($unassigned_courses as $course) {
        $row = array();
        $row[] = $course->id;
        $row[] = html_writer::link(new moodle_url('/course/view.php', array('id' => $course->id)), 
                                 $course->fullname, array('target' => '_blank'));
        $row[] = $course->shortname;
        $row[] = date('d/m/Y', $course->startdate);
        $row[] = $course->visible ? '<span class="badge bg-success">Hiển thị</span>' : '<span class="badge bg-secondary">Ẩn</span>';
        
        // Nút thêm vào đợt
        $add_url = new moodle_url($PAGE->url, array(
            'action' => 'add_course',
            'course_id' => $course->id,
            'sesskey' => sesskey()
        ));
        $row[] = html_writer::link($add_url, 'Thêm vào đợt', 
                                 array('class' => 'btn btn-sm btn-primary'));
        
        $table->data[] = $row;
    }
    
    echo html_writer::table($table);
    echo html_writer::tag('p', 'Tổng cộng: ' . count($unassigned_courses) . ' khóa học chưa gán', 
                         array('class' => 'text-muted'));
}
echo html_writer::end_div();

echo html_writer::end_div(); // End tab-content

echo $OUTPUT->footer();