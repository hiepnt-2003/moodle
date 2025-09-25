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
     * @param int $start_date Ngày bắt đầu học (timestamp)
     * @return int ID của đợt mở môn vừa tạo
     */
    public static function create_batch($batch_name, $start_date) {
        global $DB;
        
        $batch = new \stdClass();
        $batch->batch_name = $batch_name;
        $batch->start_date = $start_date;
        $batch->created_date = time();
        
        $batch_id = $DB->insert_record('local_course_batches', $batch);
        
        // Tự động thêm các khóa học có cùng startdate vào đợt
        self::auto_assign_courses_by_start_date($batch_id, $start_date);
        
        return $batch_id;
    }

    /**
     * Cập nhật đợt mở môn
     * @param int $id ID của đợt mở môn
     * @param string $batch_name Tên đợt mở môn
     * @param int $start_date Ngày bắt đầu học (timestamp)
     * @return bool True nếu thành công
     */
    public static function update_batch($id, $batch_name, $start_date) {
        global $DB;
        
        $batch = new \stdClass();
        $batch->id = $id;
        $batch->batch_name = $batch_name;
        $batch->start_date = $start_date;
        
        $result = $DB->update_record('local_course_batches', $batch);
        
        if ($result) {
            // Xóa tất cả khóa học hiện tại trong đợt
            $DB->delete_records('local_course_batch_courses', array('batchid' => $id));
            
            // Tự động thêm lại các khóa học có cùng startdate
            self::auto_assign_courses_by_start_date($id, $start_date);
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
        
        // Lấy tất cả các startdate duy nhất từ bảng course
        $sql = "SELECT DISTINCT c.startdate, COUNT(*) as course_count
                FROM {course} c
                WHERE c.startdate > 0 AND c.id > 1
                AND NOT EXISTS (
                    SELECT 1 FROM {local_course_batches} b 
                    WHERE b.start_date = c.startdate
                )
                GROUP BY c.startdate
                ORDER BY c.startdate";
        
        $start_dates = $DB->get_records_sql($sql);
        $created_count = 0;
        
        foreach ($start_dates as $dateinfo) {
            $start_date = $dateinfo->startdate;
            
            // Tạo tên đợt mở môn dựa trên ngày bắt đầu
            $batch_name = 'Đợt mở môn ' . date('d/m/Y', $start_date);
            
            // Tạo đợt mở môn (sẽ tự động assign khóa học có cùng startdate)
            self::create_batch($batch_name, $start_date);
            $created_count++;
        }
        
        return $created_count;
    }

    /**
     * Tự động gán khóa học vào đợt dựa trên ngày bắt đầu
     * @param int $batch_id ID của đợt mở môn
     * @param int $start_date Ngày bắt đầu của đợt
     * @return int Số khóa học đã gán
     */
    public static function auto_assign_courses_by_start_date($batch_id, $start_date) {
        global $DB;
        
        // Lấy tất cả khóa học có cùng startdate
        $sql = "SELECT id
                FROM {course} 
                WHERE id > 1 
                AND startdate = ?";
        
        $courses = $DB->get_records_sql($sql, array($start_date));
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
        return self::auto_assign_courses_by_start_date($batch_id, $start_date);
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
     * Lấy danh sách khóa học trong một đợt mở môn với thông tin chi tiết
     * @param int $batch_id ID của đợt mở môn
     * @return array Danh sách khóa học với thông tin đầy đủ
     */
    public static function get_courses_in_batch($batch_id) {
        global $DB;
        
        $sql = "SELECT c.id, c.fullname, c.shortname, c.startdate, c.enddate, c.visible, 
                       c.timecreated, c.timemodified, c.summary, c.format, c.lang,
                       COUNT(DISTINCT ue.id) as enrolled_users, 
                       COUNT(DISTINCT cm.id) as total_activities,
                       bc.timecreated as time_added_to_batch,
                       cat.name as category_name,
                       cat.path as category_path
                FROM {local_course_batch_courses} bc
                JOIN {course} c ON c.id = bc.courseid
                LEFT JOIN {course_categories} cat ON cat.id = c.category
                LEFT JOIN {enrol} e ON e.courseid = c.id AND e.enrol != 'guest'
                LEFT JOIN {user_enrolments} ue ON ue.enrolid = e.id AND ue.status = 0
                LEFT JOIN {course_modules} cm ON cm.course = c.id AND cm.visible = 1
                WHERE bc.batchid = ?
                GROUP BY c.id, c.fullname, c.shortname, c.startdate, c.enddate, c.visible, 
                         c.timecreated, c.timemodified, c.summary, c.format, c.lang,
                         bc.timecreated, cat.name, cat.path
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
     * Lấy thông tin chi tiết một khóa học
     * @param int $course_id ID của khóa học
     * @return object|false Thông tin chi tiết khóa học
     */
    public static function get_course_details($course_id) {
        global $DB;
        
        $sql = "SELECT c.*, cat.name as category_name, cat.path as category_path,
                       COUNT(DISTINCT ue.id) as enrolled_users,
                       COUNT(DISTINCT ue2.id) as active_users,
                       COUNT(DISTINCT cm.id) as total_activities,
                       COUNT(DISTINCT CASE WHEN cm.visible = 1 THEN cm.id END) as visible_activities,
                       COUNT(DISTINCT CASE WHEN m.name = 'assignment' THEN cm.id END) as assignments,
                       COUNT(DISTINCT CASE WHEN m.name = 'quiz' THEN cm.id END) as quizzes,
                       COUNT(DISTINCT CASE WHEN m.name = 'forum' THEN cm.id END) as forums,
                       COUNT(DISTINCT CASE WHEN m.name = 'resource' THEN cm.id END) as resources
                FROM {course} c
                LEFT JOIN {course_categories} cat ON cat.id = c.category
                LEFT JOIN {enrol} e ON e.courseid = c.id AND e.enrol != 'guest'
                LEFT JOIN {user_enrolments} ue ON ue.enrolid = e.id
                LEFT JOIN {user_enrolments} ue2 ON ue2.enrolid = e.id AND ue2.status = 0
                LEFT JOIN {course_modules} cm ON cm.course = c.id
                LEFT JOIN {modules} m ON m.id = cm.module
                WHERE c.id = ?
                GROUP BY c.id, c.fullname, c.shortname, c.idnumber, c.summary, c.summaryformat,
                         c.format, c.showgrades, c.newsitems, c.startdate, c.enddate, c.numsections,
                         c.maxbytes, c.showreports, c.visible, c.visibleold, c.groupmode, c.groupmodeforce,
                         c.defaultgroupingid, c.lang, c.calendartype, c.theme, c.timecreated, c.timemodified,
                         c.requested, c.enablecompletion, c.completionnotify, c.cacherev, c.category,
                         cat.name, cat.path";
        
        return $DB->get_record_sql($sql, array($course_id));
    }

    /**
     * Lấy danh sách giáo viên của một khóa học
     * @param int $course_id ID của khóa học
     * @return array Danh sách giáo viên
     */
    public static function get_course_teachers($course_id) {
        global $DB;
        
        $sql = "SELECT DISTINCT u.id, u.firstname, u.lastname, u.email, r.name as role_name, r.shortname as role_shortname
                FROM {user} u
                JOIN {role_assignments} ra ON ra.userid = u.id
                JOIN {role} r ON r.id = ra.roleid
                JOIN {context} ctx ON ctx.id = ra.contextid
                WHERE ctx.contextlevel = 50 AND ctx.instanceid = ?
                AND r.shortname IN ('editingteacher', 'teacher')
                ORDER BY r.shortname, u.lastname, u.firstname";
        
        return $DB->get_records_sql($sql, array($course_id));
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