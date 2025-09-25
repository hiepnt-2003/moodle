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
 * Main page for local_course_batches plugin
 *
 * @package    local_course_batches
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->libdir . '/tablelib.php');
require_once($CFG->libdir . '/formslib.php');

use local_course_batches\batch_manager;

// Kiểm tra đăng nhập
require_login();

// Kiểm tra quyền truy cập
$context = context_system::instance();
require_capability('local/course_batches:view', $context);

// Lấy tham số
$action = optional_param('action', 'list', PARAM_ALPHA);
$id = optional_param('id', 0, PARAM_INT);
$confirm = optional_param('confirm', 0, PARAM_INT);

// Thiết lập trang
$PAGE->set_url('/local/course_batches/index.php', array('action' => $action, 'id' => $id));
$PAGE->set_context($context);
$PAGE->set_title(get_string('pluginname', 'local_course_batches'));
$PAGE->set_heading(get_string('pluginname', 'local_course_batches'));
$PAGE->set_pagelayout('admin');

// Xử lý các action
switch ($action) {
    case 'delete':
        if ($id && has_capability('local/course_batches:manage', $context)) {
            if ($confirm && confirm_sesskey()) {
                batch_manager::delete_batch($id);
                redirect(new moodle_url('/local/course_batches/index.php'), 
                        get_string('batch_deleted', 'local_course_batches'), 
                        null, \core\output\notification::NOTIFY_SUCCESS);
            } else {
                // Hiển thị trang xác nhận xóa
                echo $OUTPUT->header();
                $batch = batch_manager::get_batch($id);
                if ($batch) {
                    $confirmurl = new moodle_url('/local/course_batches/index.php', 
                                                array('action' => 'delete', 'id' => $id, 'confirm' => 1, 'sesskey' => sesskey()));
                    $cancelurl = new moodle_url('/local/course_batches/index.php');
                    
                    echo $OUTPUT->confirm(
                        get_string('confirm_delete', 'local_course_batches') . '<br><strong>' . $batch->batch_name . '</strong>',
                        $confirmurl,
                        $cancelurl
                    );
                }
                echo $OUTPUT->footer();
                exit;
            }
        }
        break;
        
    case 'auto_generate':
        if (has_capability('local/course_batches:manage', $context) && confirm_sesskey()) {
            $count = batch_manager::auto_generate_batches();
            redirect(new moodle_url('/local/course_batches/index.php'), 
                    get_string('generate_success', 'local_course_batches', $count), 
                    null, \core\output\notification::NOTIFY_SUCCESS);
        }
        break;
        
    case 'view_courses':
        // Hiển thị khóa học trong đợt
        if ($id) {
            $batch = batch_manager::get_batch($id);
            if ($batch) {
                echo $OUTPUT->header();
                
                // Tiêu đề
                echo $OUTPUT->heading(get_string('batch_courses', 'local_course_batches', $batch->batch_name));
                
                // Nút quay lại
                $back_url = new moodle_url('/local/course_batches/index.php');
                echo html_writer::link($back_url, get_string('back_to_batches', 'local_course_batches'), 
                                     array('class' => 'btn btn-secondary mb-3'));
                
                // Lấy danh sách khóa học
                $courses = batch_manager::get_courses_in_batch($batch->start_date);
                
                if (empty($courses)) {
                    echo $OUTPUT->notification('Không có khóa học nào trong đợt này.', 'info');
                } else {
                    // Tạo bảng hiển thị khóa học
                    $table = new html_table();
                    $table->head = array(
                        'ID',
                        'Tên khóa học',
                        'Tên viết tắt',
                        'Ngày bắt đầu',
                        'Trạng thái',
                        'Số học viên'
                    );
                    $table->attributes['class'] = 'table table-striped';
                    
                    foreach ($courses as $course) {
                        $row = array();
                        $row[] = $course->id;
                        $row[] = html_writer::link(new moodle_url('/course/view.php', array('id' => $course->id)), 
                                                 $course->fullname, array('target' => '_blank'));
                        $row[] = $course->shortname;
                        $row[] = date('d/m/Y', $course->startdate);
                        $row[] = $course->visible ? 'Hiển thị' : 'Ẩn';
                        $row[] = $course->enrolled_users;
                        $table->data[] = $row;
                    }
                    
                    echo html_writer::table($table);
                }
                
                echo $OUTPUT->footer();
                exit;
            }
        }
        break;
}

// Hiển thị trang chính (danh sách đợt mở môn)
echo $OUTPUT->header();

// Tiêu đề trang
echo $OUTPUT->heading(get_string('batch_list', 'local_course_batches'));

// Nút thêm mới và tự động tạo (chỉ hiển thị cho người có quyền quản lý)
if (has_capability('local/course_batches:manage', $context)) {
    echo html_writer::start_div('mb-3');
    
    $add_url = new moodle_url('/local/course_batches/manage.php', array('action' => 'add'));
    echo html_writer::link($add_url, get_string('add_batch', 'local_course_batches'), 
                         array('class' => 'btn btn-primary me-2'));
    
    $generate_url = new moodle_url('/local/course_batches/index.php', 
                                  array('action' => 'auto_generate', 'sesskey' => sesskey()));
    echo html_writer::link($generate_url, get_string('auto_generate', 'local_course_batches'), 
                         array('class' => 'btn btn-success'));
    
    echo html_writer::end_div();
}

// Lấy danh sách đợt mở môn
$batches = batch_manager::get_all_batches();

if (empty($batches)) {
    echo $OUTPUT->notification(get_string('no_batches', 'local_course_batches'), 'info');
} else {
    // Tạo bảng hiển thị
    $table = new html_table();
    $table->head = array(
        get_string('batch_name', 'local_course_batches'),
        get_string('start_date', 'local_course_batches'),
        get_string('created_date', 'local_course_batches'),
        get_string('course_count', 'local_course_batches'),
        get_string('actions', 'local_course_batches')
    );
    $table->attributes['class'] = 'table table-striped';
    
    foreach ($batches as $batch) {
        $row = array();
        $row[] = $batch->batch_name;
        $row[] = date('d/m/Y', $batch->start_date);
        $row[] = date('d/m/Y H:i', $batch->created_date);
        $row[] = $batch->course_count;
        
        // Cột actions
        $actions = array();
        
        // Xem khóa học
        $view_url = new moodle_url('/local/course_batches/index.php', 
                                  array('action' => 'view_courses', 'id' => $batch->id));
        $actions[] = html_writer::link($view_url, get_string('view_courses', 'local_course_batches'), 
                                     array('class' => 'btn btn-sm btn-info'));
        
        if (has_capability('local/course_batches:manage', $context)) {
            // Sửa
            $edit_url = new moodle_url('/local/course_batches/manage.php', 
                                      array('action' => 'edit', 'id' => $batch->id));
            $actions[] = html_writer::link($edit_url, get_string('edit_batch', 'local_course_batches'), 
                                         array('class' => 'btn btn-sm btn-warning'));
            
            // Xóa
            $delete_url = new moodle_url('/local/course_batches/index.php', 
                                        array('action' => 'delete', 'id' => $batch->id));
            $actions[] = html_writer::link($delete_url, get_string('delete_batch', 'local_course_batches'), 
                                         array('class' => 'btn btn-sm btn-danger'));
        }
        
        $row[] = implode(' ', $actions);
        $table->data[] = $row;
    }
    
    echo html_writer::table($table);
}

echo $OUTPUT->footer();