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
 * English language strings for local_createtable plugin.
 *
 * @package    local_createtable
 * @copyright  2025 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Create Table Manager';
$string['batchlist'] = 'Danh sách đợt mở môn';
$string['batchname'] = 'Tên đợt';
$string['opendate'] = 'Ngày mở môn';
$string['timecreated'] = 'Ngày tạo';
$string['actions'] = 'Thao tác';
$string['nobatches'] = 'Chưa có đợt mở môn nào.';
$string['addbatch'] = 'Thêm đợt mở môn mới';
$string['editbatch'] = 'Sửa đợt mở môn';
$string['edit'] = 'Sửa';
$string['view'] = 'Xem';
$string['save'] = 'Lưu';
$string['cancel'] = 'Hủy';
$string['back'] = 'Quay lại';

// Batch detail page
$string['batchdetail'] = 'Chi tiết đợt mở môn';
$string['batchinfo'] = 'Thông tin đợt';
$string['courseslist'] = 'Danh sách môn học';
$string['coursename'] = 'Tên môn học';
$string['shortname'] = 'Tên viết tắt';
$string['dateadded'] = 'Ngày thêm';
$string['nocourses'] = 'Chưa có môn học nào trong đợt này.';

// Form strings
$string['batchname_placeholder'] = 'Nhập tên đợt mở môn...';
$string['batchname_help'] = 'Tên mô tả cho đợt mở môn này (ví dụ: Đợt 1/2025, Học kỳ I 2024-2025)';
$string['opendate_help'] = 'Ngày bắt đầu mở đăng ký môn học trong đợt này';
$string['batchname_required'] = 'Vui lòng nhập tên đợt mở môn';
$string['opendate_required'] = 'Vui lòng chọn ngày mở môn';
$string['batchname_toolong'] = 'Tên đợt mở môn không được vượt quá 255 ký tự';
$string['opendate_past'] = 'Ngày mở môn không được là ngày trong quá khứ';

// Success/Error messages
$string['batchcreated'] = 'Đã tạo đợt mở môn thành công';
$string['batchupdated'] = 'Đã cập nhật đợt mở môn thành công';
$string['batchdeleted'] = 'Đã xóa đợt mở môn thành công';
$string['batchcreatefailed'] = 'Tạo đợt mở môn thất bại';
$string['batchupdatefailed'] = 'Cập nhật đợt mở môn thất bại';
$string['batchdeletefailed'] = 'Xóa đợt mở môn thất bại';
$string['batchnotfound'] = 'Không tìm thấy đợt mở môn';

// Auto-course assignment
$string['startdate'] = 'Ngày bắt đầu';
$string['statistics'] = 'Thống kê';
$string['totalcourses'] = 'Tổng môn học';
$string['automatched'] = 'Tự động thêm';
$string['availablecourses'] = 'Môn có sẵn';
$string['automaticallyadded'] = 'Được thêm tự động';
$string['manuallyadded'] = 'Được thêm thủ công';
$string['addmethod'] = 'Phương thức thêm';
$string['automatic'] = 'Tự động';
$string['manual'] = 'Thủ công';
$string['refresh_courses'] = 'Cập nhật tự động';
$string['autocoursehint'] = 'Các môn học bắt đầu vào ngày';
$string['willbeautoadded'] = 'sẽ được tự động thêm vào đợt này.';
$string['coursesautoadded'] = 'Đã tự động thêm {$a} môn học vào đợt.';
$string['nocoursestoadd'] = 'Không có môn học mới nào để thêm.';
$string['errorrefreshingcourses'] = 'Lỗi khi cập nhật môn học';
$string['confirmrefreshcourses'] = 'Tìm thấy {$a} môn học mới có thể thêm vào đợt này:';
$string['proceedrefresh'] = 'Bạn có muốn thêm các môn học này vào đợt không?';

// Settings strings.
$string['autoassign_enabled'] = 'Bật tự động phân công';
$string['autoassign_enabled_desc'] = 'Tự động thêm môn học vào đợt dựa trên ngày bắt đầu';
$string['default_batch_prefix'] = 'Tiền tố đợt mặc định';
$string['default_batch_prefix_desc'] = 'Tiền tố sẽ được sử dụng khi tạo tên đợt tự động';
$string['management'] = 'Quản lý';
$string['managebatches'] = 'Quản lý đợt mở môn';

// Scheduled task strings.
$string['monthlycoursecreationnametask'] = 'Tạo đợt môn học hàng tháng';
$string['monthlybatchname'] = 'Đợt tháng {$a}';

// Privacy.
$string['privacy:metadata'] = 'Plugin Create Table Manager chỉ lưu trữ dữ liệu về các đợt mở môn và không lưu trữ dữ liệu cá nhân của người dùng.';
