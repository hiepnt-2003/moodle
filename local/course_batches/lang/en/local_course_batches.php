<?php<?php

// This file is part of Moodle - http://moodle.org/// This file is part of Moodle - http://moodle.org/

////

// Moodle is free software: you can redistribute it and/or modify// Moodle is free software: you can redistribute it and/or modify

// it under the terms of the GNU General Public License as published by// it under the terms of the GNU General Public License as published by

// the Free Software Foundation, either version 3 of the License, or// the Free Software Foundation, either version 3 of the License, or

// (at your option) any later version.// (at your option) any later version.

////

// Moodle is distributed in the hope that it will be useful,// Moodle is distributed in the hope that it will be useful,

// but WITHOUT ANY WARRANTY; without even the implied warranty of// but WITHOUT ANY WARRANTY; without even the implied warranty of

// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the

// GNU General Public License for more details.// GNU General Public License for more details.

////

// You should have received a copy of the GNU General Public License// You should have received a copy of the GNU General Public License

// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.



/**/**

 * English language strings for local_course_batches plugin * English language strings for local_course_batches plugin

 * *

 * @package    local_course_batches * @package    local_course_batches

 * @copyright  2025 Your Name * @copyright  2025 Your Name

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

 */ */



defined('MOODLE_INTERNAL') || die();defined('MOODLE_INTERNAL') || die();



$string['pluginname'] = 'Quản lý đợt mở môn';$string['pluginname'] = 'Quản lý đợt mở môn';

$string['course_batches:view'] = 'Xem đợt mở môn';$string['course_batches:view'] = 'Xem đợt mở môn';

$string['course_batches:manage'] = 'Quản lý đợt mở môn';$string['course_batches:manage'] = 'Quản lý đợt mở môn';

$string['batch_name'] = 'Tên đợt mở môn';

// Batch management$string['start_date'] = 'Ngày bắt đầu học';

$string['batch_name'] = 'Tên đợt mở môn';$string['start_date_help'] = 'Ngày bắt đầu học của đợt mở môn. Tất cả khóa học có cùng ngày bắt đầu này sẽ được tự động thêm vào đợt.';

$string['timecreated'] = 'Ngày tạo';$string['created_date'] = 'Ngày tạo';

$string['add_batch'] = 'Thêm đợt mở môn';$string['course_count'] = 'Số khóa học';

$string['edit_batch'] = 'Sửa đợt mở môn';$string['manage_batches'] = 'Quản lý đợt mở môn';

$string['delete_batch'] = 'Xóa đợt mở môn';$string['add_batch'] = 'Thêm đợt mở môn';

$string['view_batch'] = 'Xem đợt học';$string['edit_batch'] = 'Sửa đợt mở môn';

$string['batch_list'] = 'Danh sách đợt mở môn';$string['delete_batch'] = 'Xóa đợt mở môn';

$string['no_batches'] = 'Chưa có đợt mở môn nào được tạo';$string['batch_list'] = 'Danh sách đợt mở môn';

$string['no_batches'] = 'Chưa có đợt mở môn nào được tạo';

// Batch operations$string['batch_created'] = 'Đợt mở môn đã được tạo thành công';

$string['batch_created'] = 'Đợt mở môn đã được tạo thành công';$string['batch_updated'] = 'Đợt mở môn đã được cập nhật thành công';

$string['batch_updated'] = 'Đợt mở môn đã được cập nhật thành công';$string['batch_deleted'] = 'Đợt mở môn đã được xóa thành công';

$string['batch_deleted'] = 'Đợt mở môn đã được xóa thành công';$string['confirm_delete'] = 'Bạn có chắc chắn muốn xóa đợt mở môn này?';

$string['confirm_delete'] = 'Bạn có chắc chắn muốn xóa đợt mở môn này?';$string['auto_generate'] = 'Tự động tạo đợt từ khóa học';

$string['generate_success'] = 'Đã tự động tạo {$a} đợt mở môn từ dữ liệu khóa học';

// Course management$string['actions'] = 'Thao tác';

$string['course_count'] = 'Số môn học';$string['view_courses'] = 'Xem khóa học';

$string['courses_in_batch'] = 'Môn học trong đợt';$string['batch_courses'] = 'Khóa học trong đợt {$a}';

$string['no_courses_in_batch'] = 'Chưa có môn học nào trong đợt này';$string['back_to_batches'] = 'Quay lại danh sách đợt';

$string['auto_assign_courses'] = 'Tự động thêm môn học';$string['manage_courses_in_batch'] = 'Quản lý khóa học trong đợt';

$string['course_added_to_batch'] = 'Đã thêm môn học vào đợt';$string['courses_in_batch'] = 'Khóa học trong đợt';

$string['course_removed_from_batch'] = 'Đã xóa môn học khỏi đợt';$string['unassigned_courses'] = 'Khóa học chưa gán';

$string['add_course_to_batch'] = 'Thêm vào đợt';

// Statistics$string['remove_course_from_batch'] = 'Xóa khỏi đợt';

$string['total_batches'] = 'Tổng số đợt mở môn';$string['auto_assign_courses'] = 'Tự động gán khóa học';

$string['total_courses'] = 'Tổng số môn học';$string['course_added_to_batch'] = 'Đã thêm khóa học vào đợt';

$string['assigned_courses'] = 'Môn học đã gán';$string['course_removed_from_batch'] = 'Đã xóa khóa học khỏi đợt';

$string['unassigned_courses'] = 'Môn học chưa gán';$string['time_added_to_batch'] = 'Ngày thêm vào đợt';

$string['total_batches'] = 'Tổng số đợt mở môn';

// Actions$string['total_courses'] = 'Tổng số khóa học';

$string['actions'] = 'Thao tác';$string['assigned_courses'] = 'Khóa học đã gán';

$string['back_to_batches'] = 'Quay lại danh sách đợt';$string['unassigned_courses_count'] = 'Khóa học chưa gán';

$string['statistics'] = 'Thống kê tổng quan';

// Table headers$string['batch_details'] = 'Chi tiết đợt mở môn';

$string['course_name'] = 'Tên môn học';$string['course_relationship'] = 'Mối liên hệ với khóa học';

$string['course_shortname'] = 'Mã môn học';$string['no_courses_in_batch'] = 'Chưa có khóa học nào được gán vào đợt này';

$string['course_startdate'] = 'Ngày bắt đầu';$string['all_courses_assigned'] = 'Tất cả khóa học đã được gán vào các đợt';

$string['course_category'] = 'Danh mục';$string['course_date_range'] = 'Thời gian khóa học';

$string['course_visible'] = 'Hiển thị';$string['auto_assign_by_start_date'] = 'Tự động gán theo ngày bắt đầu';

$string['enrolled_users'] = 'Số học viên';$string['courses_with_same_start_date'] = 'Khóa học có cùng ngày bắt đầu';
$string['batch_start_date'] = 'Ngày bắt đầu đợt: {$a}';
$string['course_start_date'] = 'Ngày bắt đầu khóa học: {$a}';