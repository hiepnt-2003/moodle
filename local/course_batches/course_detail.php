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

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once(__DIR__ . '/classes/batch_manager.php');

// Tham số
$course_id = required_param('id', PARAM_INT);
$batch_id = optional_param('batch_id', 0, PARAM_INT);

// Kiểm tra quyền truy cập
require_login();
admin_externalpage_setup('local_course_batches');

// Khởi tạo batch manager
$batch_manager = new local_course_batches\batch_manager();

// Lấy thông tin khóa học
$course = $DB->get_record('course', array('id' => $course_id), '*', MUST_EXIST);
$course_details = $batch_manager->get_course_details($course_id);
$teachers = $batch_manager->get_course_teachers($course_id);

// Lấy thông tin đợt mở môn (nếu có)
$batch_info = null;
if ($batch_id > 0) {
    $batch_info = $DB->get_record('local_course_batches', array('id' => $batch_id));
}

// Tiêu đề trang
$PAGE->set_title('Chi tiết khóa học: ' . $course->fullname);
$PAGE->set_heading('Chi tiết khóa học');

// URL trở về
$return_url = new moodle_url('/local/course_batches/index.php');
if ($batch_id > 0) {
    $return_url->param('id', $batch_id);
}

echo $OUTPUT->header();

// Breadcrumb
echo html_writer::start_div('mb-3');
echo html_writer::link($return_url, '← Quay lại danh sách', array('class' => 'btn btn-secondary'));
echo html_writer::end_div();

// Tiêu đề
echo html_writer::tag('h2', $course->fullname, array('class' => 'mb-4'));

// Thông tin cơ bản
echo html_writer::start_div('row mb-4');

// Cột trái - Thông tin chính
echo html_writer::start_div('col-md-8');
echo html_writer::start_div('card');
echo html_writer::start_div('card-header');
echo html_writer::tag('h5', 'Thông tin khóa học', array('class' => 'card-title mb-0'));
echo html_writer::end_div();
echo html_writer::start_div('card-body');

$course_info_table = new html_table();
$course_info_table->attributes['class'] = 'table table-borderless';
$course_info_table->data = array(
    array('Tên khóa học:', html_writer::tag('strong', $course->fullname)),
    array('Mã khóa học:', html_writer::tag('code', $course->shortname)),
    array('ID khóa học:', $course->id),
    array('Danh mục:', $course_details->category_name ?: 'Không xác định'),
    array('Định dạng khóa học:', ucfirst($course->format ?: 'topics')),
    array('Ngôn ngữ:', $course->lang ?: 'Mặc định'),
    array('Ngày tạo:', date('d/m/Y H:i', $course->timecreated)),
    array('Cập nhật lần cuối:', date('d/m/Y H:i', $course->timemodified)),
    array('Ngày bắt đầu khóa học:', date('d/m/Y', $course->startdate)),
);

if (!empty($course->enddate) && $course->enddate > 0) {
    $course_info_table->data[] = array('Ngày kết thúc khóa học:', date('d/m/Y', $course->enddate));
}

$course_info_table->data[] = array('Trạng thái:', 
    $course->visible ? '<span class="badge bg-success">Hiển thị</span>' : '<span class="badge bg-secondary">Ẩn</span>'
);

echo html_writer::table($course_info_table);

// Mô tả khóa học
if (!empty($course->summary)) {
    echo html_writer::tag('h6', 'Mô tả khóa học:', array('class' => 'mt-3'));
    echo html_writer::start_div('p-3 bg-light rounded');
    echo format_text($course->summary, $course->summaryformat);
    echo html_writer::end_div();
}

echo html_writer::end_div(); // card-body
echo html_writer::end_div(); // card
echo html_writer::end_div(); // col-md-8

// Cột phải - Thống kê
echo html_writer::start_div('col-md-4');

// Thông tin đợt mở môn
if ($batch_info) {
    echo html_writer::start_div('card mb-3');
    echo html_writer::start_div('card-header bg-primary text-white');
    echo html_writer::tag('h6', 'Thông tin đợt mở môn', array('class' => 'card-title mb-0'));
    echo html_writer::end_div();
    echo html_writer::start_div('card-body');
    echo html_writer::tag('strong', $batch_info->batch_name);
    echo html_writer::empty_tag('br');
    echo html_writer::tag('small', 'Thời gian đợt: ' . date('d/m/Y', $batch_info->start_date) . ' - ' . date('d/m/Y', $batch_info->end_date), 
                         array('class' => 'text-muted'));
    echo html_writer::end_div();
    echo html_writer::end_div();
}

// Thống kê học viên
echo html_writer::start_div('card mb-3');
echo html_writer::start_div('card-header bg-success text-white');
echo html_writer::tag('h6', 'Thống kê học viên', array('class' => 'card-title mb-0'));
echo html_writer::end_div();
echo html_writer::start_div('card-body text-center');
echo html_writer::tag('h3', $course_details->enrolled_users, array('class' => 'text-success mb-1'));
echo html_writer::tag('small', 'Tổng số học viên', array('class' => 'text-muted'));
echo html_writer::empty_tag('br');
echo html_writer::tag('h5', $course_details->active_users, array('class' => 'text-primary mb-1'));
echo html_writer::tag('small', 'Học viên đang hoạt động', array('class' => 'text-muted'));
echo html_writer::end_div();
echo html_writer::end_div();

// Thống kê hoạt động
echo html_writer::start_div('card mb-3');
echo html_writer::start_div('card-header bg-info text-white');
echo html_writer::tag('h6', 'Thống kê hoạt động', array('class' => 'card-title mb-0'));
echo html_writer::end_div();
echo html_writer::start_div('card-body');
echo html_writer::tag('h4', $course_details->total_activities, array('class' => 'text-center text-info mb-3'));
echo html_writer::tag('p', 'Tổng số hoạt động', array('class' => 'text-center text-muted mb-3'));

$activity_stats = array(
    'Bài tập' => $course_details->assignments,
    'Bài kiểm tra' => $course_details->quizzes,
    'Diễn đàn' => $course_details->forums,
    'Tài nguyên' => $course_details->resources
);

foreach ($activity_stats as $type => $count) {
    if ($count > 0) {
        echo html_writer::start_div('d-flex justify-content-between');
        echo html_writer::tag('span', $type);
        echo html_writer::tag('span', $count, array('class' => 'badge bg-secondary'));
        echo html_writer::end_div();
    }
}
echo html_writer::end_div();
echo html_writer::end_div();

echo html_writer::end_div(); // col-md-4
echo html_writer::end_div(); // row

// Danh sách giảng viên
if (!empty($teachers)) {
    echo html_writer::tag('h4', 'Danh sách giảng viên', array('class' => 'mb-3'));
    echo html_writer::start_div('row');
    
    foreach ($teachers as $teacher) {
        echo html_writer::start_div('col-md-6 mb-3');
        echo html_writer::start_div('card');
        echo html_writer::start_div('card-body');
        
        // Avatar và thông tin
        $user_picture = $OUTPUT->user_picture($teacher, array('size' => 50, 'class' => 'float-start me-3'));
        echo $user_picture;
        
        echo html_writer::tag('h6', fullname($teacher), array('class' => 'mb-1'));
        echo html_writer::tag('small', $teacher->email, array('class' => 'text-muted d-block'));
        echo html_writer::tag('span', $teacher->role_name, array('class' => 'badge bg-primary'));
        
        echo html_writer::end_div();
        echo html_writer::end_div();
        echo html_writer::end_div();
    }
    
    echo html_writer::end_div();
}

// Liên kết hữu ích
echo html_writer::tag('h4', 'Liên kết hữu ích', array('class' => 'mb-3 mt-4'));
echo html_writer::start_div('row');

$useful_links = array(
    array(
        'title' => 'Xem khóa học',
        'url' => new moodle_url('/course/view.php', array('id' => $course->id)),
        'class' => 'btn-primary',
        'target' => '_blank'
    ),
    array(
        'title' => 'Chỉnh sửa khóa học',
        'url' => new moodle_url('/course/edit.php', array('id' => $course->id)),
        'class' => 'btn-warning',
        'target' => '_blank'
    ),
    array(
        'title' => 'Quản lý học viên',
        'url' => new moodle_url('/enrol/users.php', array('id' => $course->id)),
        'class' => 'btn-success',
        'target' => '_blank'
    ),
    array(
        'title' => 'Báo cáo khóa học',
        'url' => new moodle_url('/report/outline/index.php', array('id' => $course->id)),
        'class' => 'btn-info',
        'target' => '_blank'
    )
);

foreach ($useful_links as $link) {
    echo html_writer::start_div('col-md-3 mb-2');
    $attributes = array(
        'class' => 'btn ' . $link['class'] . ' w-100',
        'target' => $link['target']
    );
    echo html_writer::link($link['url'], $link['title'], $attributes);
    echo html_writer::end_div();
}

echo html_writer::end_div();

echo $OUTPUT->footer();