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
 * Test Event API functionality page.
 *
 * @package    local_testeventapi
 * @copyright  2025 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

// Check login and capabilities.
require_login();
require_capability('local/testeventapi:manage', context_system::instance());

$action = optional_param('action', '', PARAM_ALPHA);

$PAGE->set_url(new moodle_url('/local/testeventapi/test_api.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title('Test Event API');
$PAGE->set_heading('Test Event API');

echo $OUTPUT->header();

// Back button.
$backurl = new moodle_url('/local/testeventapi/index.php');
echo html_writer::div(
    html_writer::link($backurl, get_string('back', 'local_testeventapi'), ['class' => 'btn btn-secondary']),
    'mb-3'
);

echo $OUTPUT->heading('Test Event API Functionality');

if ($action === 'test') {
    echo $OUTPUT->box_start('generalbox');
    echo html_writer::tag('h4', 'Kết quả test Event API:');
    
    try {
        // Call the test function from observer.
        ob_start();
        \local_testeventapi\observer::test_event_api();
        $output = ob_get_clean();
        
        // Display the output.
        echo html_writer::tag('pre', $output, ['class' => 'alert alert-info']);
        
        echo html_writer::tag('p', 'Test hoàn thành! Kiểm tra danh sách đợt học để xem kết quả.', 
            ['class' => 'alert alert-success']);
        
    } catch (Exception $e) {
        echo html_writer::tag('p', 'Lỗi: ' . $e->getMessage(), ['class' => 'alert alert-danger']);
    }
    
    echo $OUTPUT->box_end();
} else {
    // Show test options.
    echo $OUTPUT->box_start('generalbox');
    echo html_writer::tag('h4', 'Test Event API');
    echo html_writer::tag('p', 'Plugin này demo cách sử dụng Event API trong Moodle để tự động thêm môn học vào đợt khi có sự kiện tạo đợt mới.');
    
    echo html_writer::tag('h5', 'Các Event được implement:');
    echo html_writer::start_tag('ul');
    echo html_writer::tag('li', '<strong>batch_created:</strong> Khi tạo đợt học mới');
    echo html_writer::tag('li', '<strong>batch_updated:</strong> Khi cập nhật đợt học');
    echo html_writer::tag('li', '<strong>course_added_to_batch:</strong> Khi môn học được thêm vào đợt');
    echo html_writer::end_tag('ul');
    
    echo html_writer::tag('h5', 'Observer Events:');
    echo html_writer::start_tag('ul');
    echo html_writer::tag('li', 'Observer lắng nghe sự kiện batch_created và tự động quét tất cả môn học trong Moodle');
    echo html_writer::tag('li', 'Các môn học có ngày bắt đầu trùng với ngày bắt đầu đợt sẽ được tự động thêm vào');
    echo html_writer::tag('li', 'Quá trình này được thực hiện thông qua Event API, không cần can thiệp thủ công');
    echo html_writer::end_tag('ul');
    
    $testurl = new moodle_url('/local/testeventapi/test_api.php', ['action' => 'test']);
    echo html_writer::div(
        html_writer::link($testurl, 'Chạy Test Event API', ['class' => 'btn btn-primary btn-lg']),
        'text-center mt-4'
    );
    
    echo $OUTPUT->box_end();
    
    // Show current statistics.
    echo $OUTPUT->box_start('generalbox info');
    echo html_writer::tag('h4', 'Thống kê hiện tại:');
    
    global $DB;
    
    $totalbatches = $DB->count_records('local_testeventapi_batches');
    $totalcourses = $DB->count_records('local_testeventapi_courses');
    $eventcourses = $DB->count_records('local_testeventapi_courses', ['added_by_event' => 1]);
    $manualcourses = $DB->count_records('local_testeventapi_courses', ['added_by_event' => 0]);
    
    $statstable = new html_table();
    $statstable->attributes['class'] = 'table table-bordered';
    $statstable->data = [
        ['Tổng số đợt học', $totalbatches],
        ['Tổng số môn học trong các đợt', $totalcourses],
        ['Môn học thêm qua Event API', $eventcourses . ' (' . ($totalcourses > 0 ? round($eventcourses/$totalcourses*100, 1) : 0) . '%)'],
        ['Môn học thêm thủ công', $manualcourses . ' (' . ($totalcourses > 0 ? round($manualcourses/$totalcourses*100, 1) : 0) . '%)'],
    ];
    
    echo html_writer::table($statstable);
    echo $OUTPUT->box_end();
}

// Show recent events (if any).
echo $OUTPUT->box_start('generalbox');
echo html_writer::tag('h4', 'Hướng dẫn sử dụng:');
echo html_writer::start_tag('ol');
echo html_writer::tag('li', 'Tạo một vài môn học với ngày bắt đầu khác nhau trong Moodle');
echo html_writer::tag('li', 'Tạo đợt học mới với ngày bắt đầu trùng với một số môn học');
echo html_writer::tag('li', 'Quan sát cách Event API tự động thêm các môn học có cùng ngày bắt đầu');
echo html_writer::tag('li', 'Kiểm tra trong chi tiết đợt học để thấy môn nào được thêm bằng Event API');
echo html_writer::tag('li', 'Sử dụng nút "Test Event API" để tạo một đợt test và xem kết quả');
echo html_writer::end_tag('ol');
echo $OUTPUT->box_end();

echo $OUTPUT->footer();