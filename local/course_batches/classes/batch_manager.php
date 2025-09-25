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
        
        $sql = "SELECT b.*, COUNT(c.id) as course_count
                FROM {local_course_batches} b
                LEFT JOIN {course} c ON c.startdate = b.start_date AND c.id > 1
                GROUP BY b.id, b.batch_name, b.start_date, b.created_date
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
     * @return int ID của đợt mở môn vừa tạo
     */
    public static function create_batch($batch_name, $start_date) {
        global $DB;
        
        $batch = new \stdClass();
        $batch->batch_name = $batch_name;
        $batch->start_date = $start_date;
        $batch->created_date = time();
        
        return $DB->insert_record('local_course_batches', $batch);
    }

    /**
     * Cập nhật đợt mở môn
     * @param int $id ID của đợt mở môn
     * @param string $batch_name Tên đợt mở môn
     * @param int $start_date Ngày bắt đầu (timestamp)
     * @return bool True nếu thành công
     */
    public static function update_batch($id, $batch_name, $start_date) {
        global $DB;
        
        $batch = new \stdClass();
        $batch->id = $id;
        $batch->batch_name = $batch_name;
        $batch->start_date = $start_date;
        
        return $DB->update_record('local_course_batches', $batch);
    }

    /**
     * Xóa đợt mở môn
     * @param int $id ID của đợt mở môn
     * @return bool True nếu thành công
     */
    public static function delete_batch($id) {
        global $DB;
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
                self::create_batch($batch_name, $start_date);
                $created_count++;
            }
        }
        
        return $created_count;
    }

    /**
     * Lấy danh sách khóa học trong một đợt mở môn
     * @param int $start_date Ngày bắt đầu của đợt
     * @return array Danh sách khóa học
     */
    public static function get_courses_in_batch($start_date) {
        global $DB;
        
        $sql = "SELECT c.id, c.fullname, c.shortname, c.startdate, c.visible,
                       COUNT(ue.id) as enrolled_users
                FROM {course} c
                LEFT JOIN {enrol} e ON e.courseid = c.id
                LEFT JOIN {user_enrolments} ue ON ue.enrolid = e.id
                WHERE c.startdate = ? AND c.id > 1
                GROUP BY c.id, c.fullname, c.shortname, c.startdate, c.visible
                ORDER BY c.fullname";
        
        return $DB->get_records_sql($sql, array($start_date));
    }
}