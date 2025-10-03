<?php
// This file is part of Moodle - http://moodle.org/

/**
 * Simplified external web service functions for course copying.
 *
 * @package    local_coursecopier
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/course/lib.php');

/**
 * Course copier external functions
 */
class local_coursecopier_external extends external_api {

    /**
     * Copy course parameters
     */
    public static function copy_course_parameters() {
        return new external_function_parameters([
            'shortname_clone' => new external_value(PARAM_TEXT, 'Shortname của môn học cần copy'),
            'fullname' => new external_value(PARAM_TEXT, 'Tên đầy đủ của môn học mới'),  
            'shortname' => new external_value(PARAM_TEXT, 'Shortname của môn học mới'),
            'startdate' => new external_value(PARAM_INT, 'Ngày bắt đầu (timestamp)'),
            'enddate' => new external_value(PARAM_INT, 'Ngày kết thúc (timestamp)'),
        ]);
    }

    /**
     * Copy course function
     */
    public static function copy_course($shortname_clone, $fullname, $shortname, $startdate, $enddate) {
        global $DB, $CFG;
        
        // Validate parameters
        $params = self::validate_parameters(self::copy_course_parameters(), [
            'shortname_clone' => $shortname_clone,
            'fullname' => $fullname,
            'shortname' => $shortname,
            'startdate' => $startdate,
            'enddate' => $enddate,
        ]);
        
        // Check capabilities
        $context = context_system::instance();
        require_capability('moodle/course:create', $context);
        
        try {
            // Find source course
            $source_course = $DB->get_record('course', ['shortname' => $params['shortname_clone']]);
            if (!$source_course) {
                return [
                    'status' => 'error',
                    'id' => 0,
                    'message' => 'Không tìm thấy môn học nguồn: ' . $params['shortname_clone']
                ];
            }
            
            // Check if target shortname already exists
            if ($DB->record_exists('course', ['shortname' => $params['shortname']])) {
                return [
                    'status' => 'error',
                    'id' => 0,
                    'message' => 'Shortname đã tồn tại: ' . $params['shortname']
                ];
            }
            
            // Create new course
            $coursedata = new stdClass();
            $coursedata->category = $source_course->category;
            $coursedata->fullname = $params['fullname'];
            $coursedata->shortname = $params['shortname'];
            $coursedata->startdate = $params['startdate'];
            $coursedata->enddate = $params['enddate'];
            $coursedata->visible = 1;
            $coursedata->format = $source_course->format;
            $coursedata->numsections = $source_course->numsections;
            $coursedata->summary = 'Copied from: ' . $source_course->fullname;
            $coursedata->summaryformat = FORMAT_HTML;
            
            // Create the course
            $new_course = create_course($coursedata);
            
            if ($new_course) {
                // Simple content copy (just basic course structure)
                // For full backup/restore, additional setup is needed
                
                return [
                    'status' => 'success',
                    'id' => (int)$new_course->id,
                    'message' => 'Môn học đã được tạo thành công (cấu trúc cơ bản). ID: ' . $new_course->id
                ];
            } else {
                return [
                    'status' => 'error',
                    'id' => 0,
                    'message' => 'Không thể tạo môn học mới'
                ];
            }
            
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'id' => 0,
                'message' => 'Lỗi: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Copy course return values
     */
    public static function copy_course_returns() {
        return new external_single_structure([
            'status' => new external_value(PARAM_TEXT, 'Trạng thái: success hoặc error'),
            'id' => new external_value(PARAM_INT, 'ID của môn học đã copy (0 nếu có lỗi)'),
            'message' => new external_value(PARAM_TEXT, 'Thông báo thành công hoặc mô tả lỗi'),
        ]);
    }

    /**
     * Get available courses parameters
     */
    public static function get_available_courses_parameters() {
        return new external_function_parameters([
            'categoryid' => new external_value(PARAM_INT, 'ID danh mục (0 = tất cả)', VALUE_DEFAULT, 0),
        ]);
    }

    /**
     * Get available courses function
     */
    public static function get_available_courses($categoryid = 0) {
        global $DB;
        
        // Validate parameters
        $params = self::validate_parameters(self::get_available_courses_parameters(), [
            'categoryid' => $categoryid,
        ]);
        
        // Check capabilities
        $context = context_system::instance();
        require_capability('moodle/course:view', $context);
        
        try {
            $sql = "SELECT id, shortname, fullname, category 
                    FROM {course} 
                    WHERE id != 1"; // Exclude site course
            
            $sqlparams = [];
            
            if ($params['categoryid'] > 0) {
                $sql .= " AND category = ?";
                $sqlparams[] = $params['categoryid'];
            }
            
            $sql .= " ORDER BY fullname";
            
            $courses = $DB->get_records_sql($sql, $sqlparams);
            
            $result = [];
            foreach ($courses as $course) {
                $result[] = [
                    'id' => (int)$course->id,
                    'shortname' => $course->shortname,
                    'fullname' => $course->fullname,
                    'category' => (int)$course->category,
                ];
            }
            
            return $result;
            
        } catch (Exception $e) {
            throw new moodle_exception('error', 'local_coursecopier', '', $e->getMessage());
        }
    }

    /**
     * Get available courses return values
     */
    public static function get_available_courses_returns() {
        return new external_multiple_structure(
            new external_single_structure([
                'id' => new external_value(PARAM_INT, 'ID môn học'),
                'shortname' => new external_value(PARAM_TEXT, 'Shortname môn học'),
                'fullname' => new external_value(PARAM_TEXT, 'Tên đầy đủ môn học'),
                'category' => new external_value(PARAM_INT, 'ID danh mục'),
            ])
        );
    }
}