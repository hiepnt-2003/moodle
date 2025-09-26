<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can rdistribute it and/or modify
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
 * English language strings for local_testeventapi plugin.
 *
 * @package    local_testeventapi
 * @copyright  2025 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Test Event API';
$string['testeventapi:manage'] = 'Quản lý Test Event API';
$string['testeventapi:view'] = 'Xem Test Event API';

// Common strings
$string['no'] = 'STT';

// Main interface
$string['batchlist'] = 'Danh sách đợt học';
$string['batchname'] = 'Tên đợt';
$string['startdate'] = 'Ngày bắt đầu';
$string['timecreated'] = 'Ngày tạo';
$string['actions'] = 'Thao tác';
$string['nobatches'] = 'Chưa có đợt học nào.';
$string['addbatch'] = 'Thêm đợt học mới';
$string['editbatch'] = 'Sửa đợt học';
$string['deletebatch'] = 'Xóa đợt học';
$string['viewbatch'] = 'Xem chi tiết';
$string['edit'] = 'Sửa';
$string['view'] = 'Xem';
$string['delete'] = 'Xóa';
$string['save'] = 'Lưu';
$string['cancel'] = 'Hủy';
$string['back'] = 'Quay lại';

// Batch detail page
$string['batchdetail'] = 'Chi tiết đợt học';
$string['batchinfo'] = 'Thông tin đợt';
$string['courseslist'] = 'Danh sách môn học';
$string['coursename'] = 'Tên môn học';
$string['shortname'] = 'Tên viết tắt';
$string['dateadded'] = 'Ngày thêm';
$string['addedbyevent'] = 'Thêm qua Event';
$string['addedmanually'] = 'Thêm thủ công';
$string['nocourses'] = 'Chưa có môn học nào trong đợt này.';

// Form strings
$string['batchname_placeholder'] = 'Nhập tên đợt học...';
$string['batchname_help'] = 'Tên mô tả cho đợt học này (ví dụ: Đợt 1/2025, Học kỳ I 2024-2025)';
$string['startdate_help'] = 'Ngày bắt đầu của đợt học này';
$string['batchname_required'] = 'Vui lòng nhập tên đợt học';
$string['startdate_required'] = 'Vui lòng chọn ngày bắt đầu';
$string['batchname_toolong'] = 'Tên đợt học không được vượt quá 255 ký tự';

// Success/Error messages
$string['batchcreated'] = 'Đã tạo đợt học thành công';
$string['batchupdated'] = 'Đã cập nhật đợt học thành công';
$string['batchdeleted'] = 'Đã xóa đợt học thành công';
$string['batchcreatefailed'] = 'Tạo đợt học thất bại';
$string['batchupdatefailed'] = 'Cập nhật đợt học thất bại';
$string['batchdeletefailed'] = 'Xóa đợt học thất bại';
$string['batchnotfound'] = 'Không tìm thấy đợt học';

// Event API related
$string['eventapi'] = 'Event API';
$string['batchcreatedevent'] = 'Sự kiện tạo đợt học';
$string['coursesautoadded'] = 'Đã tự động thêm {$a} môn học qua Event API';
$string['nocoursestoadd'] = 'Không có môn học nào có cùng ngày bắt đầu';
$string['coursesaddedbulk'] = 'Thêm hàng loạt môn học';
$string['eventprocessed'] = 'Đã xử lý sự kiện thành công';
$string['eventprocessfailed'] = 'Xử lý sự kiện thất bại';

// Statistics
$string['statistics'] = 'Thống kê';
$string['totalcourses'] = 'Tổng số môn học';
$string['coursesbyevent'] = 'Thêm qua Event API';
$string['coursesmanual'] = 'Thêm thủ công';
$string['availablecourses'] = 'Môn học có thể thêm';

// Privacy
$string['privacy:metadata'] = 'Plugin Test Event API chỉ lưu trữ dữ liệu về các đợt học và không lưu trữ dữ liệu cá nhân của người dùng.';

// Management
$string['management'] = 'Quản lý';
$string['managebatches'] = 'Quản lý đợt học';
$string['eventlog'] = 'Nhật ký sự kiện';
$string['testevent'] = 'Test sự kiện';
$string['triggerevent'] = 'Kích hoạt sự kiện test';
$string['eventtriggered'] = 'Sự kiện đã được kích hoạt';
$string['eventlisteners'] = 'Event Listeners đã đăng ký';
$string['confirmdeletebatch'] = 'Bạn có chắc chắn muốn xóa đợt học này không?';

// Email related strings
$string['emailsubject_batchdeleted'] = '[{$a}] Đợt học đã được xóa: {$a->batchname}';
$string['emailnotification_sent'] = 'Email thông báo đã được gửi tới admin';
$string['emailnotification_failed'] = 'Không thể gửi email thông báo';
$string['batchdeletionemailsubject'] = 'Thông báo xóa đợt học - Test Event API';
$string['batchdeletionemailbody'] = 'Đợt học "{$a->batchname}" đã được xóa bởi {$a->username}';
$string['adminemailnotification'] = 'Gửi email thông báo cho admin khi xóa đợt học';
$string['adminemailnotification_desc'] = 'Tự động gửi email thông báo cho admin khi có đợt học bị xóa';