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
 * Batch manager class for local_course_batches plugin
 *
 * @package    local_course_batches
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_course_batches;

defined('MOODLE_INTERNAL') || die();

/**
 * Class batch_manager
 * Quản lý các thao tác với đợt mở môn
 */
class batch_manager {

    /**
     * Lấy tất cả đợt mở môn
     * @return array Danh sách đợt mở môn
     */
    public static function get_all_batches() {
        global $DB;
        
        $sql = "SELECT b.*, COUNT(bc.courseid) as course_count
                FROM {local_course_batches} b
                LEFT JOIN {local_course_batch_courses} bc ON bc.batchid = b.id
                GROUP BY b.id, b.batch_name, b.start_date, b.created_date, b.description
                ORDER BY b.start_date DESC";
        
        return $DB->get_records_sql($sql);
    }

    /**
     * Lấy một đợt mở môn theo ID
     * @param int $id ID của đợt mở môn
     * @return object|false Thông tin đợt mở môn hoặc false nếu không tìm thấy
     */
    public static function get_batch($id) {
        global $DB;
        return $DB->get_record('local_course_batches', array('id' => $id));
    }

    /**
     * Tạo đợt mở môn mới
     * @param string $batch_name Tên đợt mở môn
     * @param int $start_date Ngày bắt đầu (timestamp)
     * @param string $description Mô tả đợt mở môn
     * @return int ID của đợt mở môn vừa tạo
     */
    public static function create_batch($batch_name, $start_date, $description = '') {
        global $DB;
        
        $batch = new \stdClass();
        $batch->batch_name = $batch_name;
        $batch->start_date = $start_date;
        $batch->created_date = time();
        $batch->description = $description;
        
        return $DB->insert_record('local_course_batches', $batch);
    }

    /**
     * Cập nhật đợt mở môn
     * @param int $id ID của đợt mở môn
     * @param string $batch_name Tên đợt mở môn
     * @param int $start_date Ngày bắt đầu (timestamp)
     * @param string $description Mô tả đợt mở môn
     * @return bool True nếu thành công
     */
    public static function update_batch($id, $batch_name, $start_date, $description = '') {
        global $DB;
        
        $batch = new \stdClass();
        $batch->id = $id;
        $batch->batch_name = $batch_name;
        $batch->start_date = $start_date;
        $batch->description = $description;
        
        return $DB->update_record('local_course_batches', $batch);
    }

    /**
     * Xóa đợt mở môn
     * @param int $id ID của đợt mở môn
     * @return bool True nếu thành công
     */
    public static function delete_batch($id) {
        global $DB;
        
        // Xóa tất cả liên kết với khóa học trước
        $DB->delete_records('local_course_batch_courses', array('batchid' => $id));
        
        // Sau đó xóa đợt mở môn
        return $DB->delete_records('local_course_batches', array('id' => $id));
    }

    /**
     * Tự động tạo đợt mở môn từ dữ liệu khóa học hiện có
     * @return int Số lượng đợt đã tạo
     */
    public static function auto_generate_batches() {
        global $DB;
        
        // Lấy tất cả ngày bắt đầu duy nhất từ bảng course (trừ site course)
        $sql = "SELECT DISTINCT startdate 
                FROM {course} 
                WHERE startdate > 0 AND id > 1
                AND startdate NOT IN (
                    SELECT start_date FROM {local_course_batches}
                )
                ORDER BY startdate";
        
        $start_dates = $DB->get_records_sql($sql);
        $created_count = 0;
        
        foreach ($start_dates as $record) {
            $start_date = $record->startdate;
            
            // Tạo tên đợt mở môn dựa trên ngày
            $batch_name = 'Đợt mở môn ' . date('d/m/Y', $start_date);
            
            // Kiểm tra xem đã có đợt này chưa
            if (!$DB->record_exists('local_course_batches', array('start_date' => $start_date))) {
                $description = 'Đợt mở môn được tạo tự động từ các khóa học có ngày bắt đầu ' . date('d/m/Y', $start_date);
                $batch_id = self::create_batch($batch_name, $start_date, $description);
                
                // Tự động thêm các khóa học có cùng ngày bắt đầu vào đợt
                self::auto_assign_courses_to_batch($batch_id, $start_date);
                
                $created_count++;
            }
        }
        
        return $created_count;
    }

    /**
     * Tự động gán khóa học vào đợt dựa trên ngày bắt đầu
     * @param int $batch_id ID của đợt mở môn
     * @param int $start_date Ngày bắt đầu
     * @return int Số khóa học đã gán
     */
    public static function auto_assign_courses_to_batch($batch_id, $start_date) {
        global $DB;
        
        // Lấy tất cả khóa học có cùng ngày bắt đầu
        $courses = $DB->get_records('course', array('startdate' => $start_date), '', 'id');
        $assigned_count = 0;
        
        foreach ($courses as $course) {
            if ($course->id > 1) { // Loại trừ site course
                self::add_course_to_batch($batch_id, $course->id);
                $assigned_count++;
            }
        }
        
        return $assigned_count;
    }

    /**
     * Thêm khóa học vào đợt mở môn
     * @param int $batch_id ID đợt mở môn
     * @param int $course_id ID khóa học
     * @return bool True nếu thành công
     */
    public static function add_course_to_batch($batch_id, $course_id) {
        global $DB;
        
        // Kiểm tra xem đã tồn tại chưa
        if ($DB->record_exists('local_course_batch_courses', array('batchid' => $batch_id, 'courseid' => $course_id))) {
            return false; // Đã tồn tại
        }
        
        $record = new \stdClass();
        $record->batchid = $batch_id;
        $record->courseid = $course_id;
        $record->timecreated = time();
        
        return $DB->insert_record('local_course_batch_courses', $record);
    }

    /**
     * Xóa khóa học khỏi đợt mở môn
     * @param int $batch_id ID đợt mở môn
     * @param int $course_id ID khóa học
     * @return bool True nếu thành công
     */
    public static function remove_course_from_batch($batch_id, $course_id) {
        global $DB;
        return $DB->delete_records('local_course_batch_courses', array('batchid' => $batch_id, 'courseid' => $course_id));
    }

    /**
     * Lấy danh sách khóa học trong một đợt mở môn
     * @param int $batch_id ID của đợt mở môn
     * @return array Danh sách khóa học
     */
    public static function get_courses_in_batch($batch_id) {
        global $DB;
        
        $sql = "SELECT c.id, c.fullname, c.shortname, c.startdate, c.visible, c.timecreated,
                       COUNT(ue.id) as enrolled_users, bc.timecreated as time_added_to_batch
                FROM {local_course_batch_courses} bc
                JOIN {course} c ON c.id = bc.courseid
                LEFT JOIN {enrol} e ON e.courseid = c.id
                LEFT JOIN {user_enrolments} ue ON ue.enrolid = e.id
                WHERE bc.batchid = ?
                GROUP BY c.id, c.fullname, c.shortname, c.startdate, c.visible, c.timecreated, bc.timecreated
                ORDER BY c.fullname";
        
        return $DB->get_records_sql($sql, array($batch_id));
    }

    /**
     * Lấy danh sách khóa học chưa được gán vào đợt nào
     * @return array Danh sách khóa học
     */
    public static function get_unassigned_courses() {
        global $DB;
        
        $sql = "SELECT c.id, c.fullname, c.shortname, c.startdate, c.visible
                FROM {course} c
                WHERE c.id > 1 
                AND c.id NOT IN (
                    SELECT DISTINCT courseid 
                    FROM {local_course_batch_courses}
                )
                ORDER BY c.startdate DESC, c.fullname";
        
        return $DB->get_records_sql($sql);
    }

    /**
     * Lấy thống kê tổng quan
     * @return object Thống kê
     */
    public static function get_statistics() {
        global $DB;
        
        $stats = new \stdClass();
        
        // Tổng số đợt mở môn
        $stats->total_batches = $DB->count_records('local_course_batches');
        
        // Tổng số khóa học đã được gán
        $stats->assigned_courses = $DB->count_records('local_course_batch_courses');
        
        // Tổng số khóa học chưa gán
        $sql = "SELECT COUNT(*) 
                FROM {course} c
                WHERE c.id > 1 
                AND c.id NOT IN (
                    SELECT DISTINCT courseid 
                    FROM {local_course_batch_courses}
                )";
        $stats->unassigned_courses = $DB->count_records_sql($sql);
        
        // Tổng số khóa học
        $stats->total_courses = $DB->count_records('course') - 1; // Trừ site course
        
        return $stats;
    }
}