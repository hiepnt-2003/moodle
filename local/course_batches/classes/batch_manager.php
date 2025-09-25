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
     * @param int $start_date Ngày bắt đầu học (timestamp)
     * @param int $end_date Ngày kết thúc học (timestamp)
     * @param string $description Mô tả đợt mở môn
     * @return int ID của đợt mở môn vừa tạo
     */
    public static function create_batch($batch_name, $start_date, $end_date, $description = '') {
        global $DB;
        
        $batch = new \stdClass();
        $batch->batch_name = $batch_name;
        $batch->start_date = $start_date;
        $batch->end_date = $end_date;
        $batch->created_date = time();
        $batch->description = $description;
        
        $batch_id = $DB->insert_record('local_course_batches', $batch);
        
        // Tự động thêm các khóa học phù hợp vào đợt
        self::auto_assign_courses_by_date_range($batch_id, $start_date, $end_date);
        
        return $batch_id;
    }

    /**
     * Cập nhật đợt mở môn
     * @param int $id ID của đợt mở môn
     * @param string $batch_name Tên đợt mở môn
     * @param int $start_date Ngày bắt đầu học (timestamp)
     * @param int $end_date Ngày kết thúc học (timestamp)
     * @param string $description Mô tả đợt mở môn
     * @return bool True nếu thành công
     */
    public static function update_batch($id, $batch_name, $start_date, $end_date, $description = '') {
        global $DB;
        
        $batch = new \stdClass();
        $batch->id = $id;
        $batch->batch_name = $batch_name;
        $batch->start_date = $start_date;
        $batch->end_date = $end_date;
        $batch->description = $description;
        
        $result = $DB->update_record('local_course_batches', $batch);
        
        if ($result) {
            // Xóa tất cả khóa học hiện tại trong đợt
            $DB->delete_records('local_course_batch_courses', array('batchid' => $id));
            
            // Tự động thêm lại các khóa học phù hợp với khoảng thời gian mới
            self::auto_assign_courses_by_date_range($id, $start_date, $end_date);
        }
        
        return $result;
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
        
        // Lấy tất cả các kết hợp (startdate, enddate) duy nhất từ bảng course
        $sql = "SELECT DISTINCT c.startdate, c.enddate, COUNT(*) as course_count
                FROM {course} c
                WHERE c.startdate > 0 AND c.enddate > 0 AND c.id > 1
                AND NOT EXISTS (
                    SELECT 1 FROM {local_course_batches} b 
                    WHERE b.start_date = c.startdate AND b.end_date = c.enddate
                )
                GROUP BY c.startdate, c.enddate
                ORDER BY c.startdate";
        
        $date_ranges = $DB->get_records_sql($sql);
        $created_count = 0;
        
        foreach ($date_ranges as $range) {
            $start_date = $range->startdate;
            $end_date = $range->enddate;
            
            // Tạo tên đợt mở môn dựa trên khoảng ngày
            $batch_name = 'Đợt mở môn ' . date('d/m/Y', $start_date) . ' - ' . date('d/m/Y', $end_date);
            
            $description = "Đợt mở môn được tạo tự động từ {$range->course_count} khóa học có thời gian từ " . 
                          date('d/m/Y', $start_date) . ' đến ' . date('d/m/Y', $end_date);
            
            // Tạo đợt mở môn (sẽ tự động assign khóa học)
            self::create_batch($batch_name, $start_date, $end_date, $description);
            $created_count++;
        }
        
        return $created_count;
    }

    /**
     * Tự động gán khóa học vào đợt dựa trên khoảng thời gian
     * @param int $batch_id ID của đợt mở môn
     * @param int $start_date Ngày bắt đầu của đợt
     * @param int $end_date Ngày kết thúc của đợt
     * @return int Số khóa học đã gán
     */
    public static function auto_assign_courses_by_date_range($batch_id, $start_date, $end_date) {
        global $DB;
        
        // Lấy tất cả khóa học có thời gian bắt đầu và kết thúc nằm trong khoảng của đợt
        $sql = "SELECT id
                FROM {course} 
                WHERE id > 1 
                AND startdate >= ? AND startdate <= ?
                AND enddate >= ? AND enddate <= ?
                AND startdate > 0 AND enddate > 0";
        
        $courses = $DB->get_records_sql($sql, array($start_date, $end_date, $start_date, $end_date));
        $assigned_count = 0;
        
        foreach ($courses as $course) {
            if (self::add_course_to_batch($batch_id, $course->id)) {
                $assigned_count++;
            }
        }
        
        return $assigned_count;
    }

    /**
     * Tự động gán khóa học vào đợt dựa trên ngày bắt đầu (method cũ - giữ lại để tương thích)
     * @param int $batch_id ID của đợt mở môn
     * @param int $start_date Ngày bắt đầu
     * @return int Số khóa học đã gán
     */
    public static function auto_assign_courses_to_batch($batch_id, $start_date) {
        global $DB;
        
        // Lấy thông tin đợt để có end_date
        $batch = self::get_batch($batch_id);
        if (!$batch) {
            return 0;
        }
        
        return self::auto_assign_courses_by_date_range($batch_id, $batch->start_date, $batch->end_date);
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