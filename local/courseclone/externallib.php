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
 * External web service functions for course cloning.
 *
 * @package    local_courseclone
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/course/lib.php');

/**
 * External functions for course cloning.
 */
class local_courseclone_external extends external_api {

    /**
     * Describes the parameters for clone_course function.
     *
     * @return external_function_parameters
     */
    public static function clone_course_parameters() {
        return new external_function_parameters([
            'shortname_clone' => new external_value(PARAM_TEXT, 'Shortname của môn học nguồn cần copy'),
            'fullname' => new external_value(PARAM_TEXT, 'Tên đầy đủ cho môn học mới'),
            'shortname' => new external_value(PARAM_TEXT, 'Tên viết tắt cho môn học mới'),
            'startdate' => new external_value(PARAM_INT, 'Ngày bắt đầu (timestamp) cho môn học mới'),
            'enddate' => new external_value(PARAM_INT, 'Ngày kết thúc (timestamp) cho môn học mới'),
        ]);
    }

    /**
     * Clone a course with new details, keeping the same category.
     *
     * @param string $shortname_clone Shortname của môn học nguồn
     * @param string $fullname Tên đầy đủ cho môn học mới
     * @param string $shortname Tên viết tắt cho môn học mới  
     * @param int $startdate Ngày bắt đầu timestamp
     * @param int $enddate Ngày kết thúc timestamp
     * @return array Kết quả với status, id, và message
     */
    public static function clone_course($shortname_clone, $fullname, $shortname, $startdate, $enddate) {
        global $DB, $USER, $CFG;

        // Validate parameters.
        $params = self::validate_parameters(self::clone_course_parameters(), [
            'shortname_clone' => $shortname_clone,
            'fullname' => $fullname,
            'shortname' => $shortname,
            'startdate' => $startdate,
            'enddate' => $enddate,
        ]);

        // Validate context and capabilities.
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('moodle/course:create', $context);

        try {
            // Step 1: Validate input parameters.
            $validation_result = self::validate_clone_parameters($params);
            if (!$validation_result['success']) {
                return [
                    'status' => 'error',
                    'id' => 0,
                    'message' => $validation_result['message']
                ];
            }

            // Step 2: Get source course.
            $source_course = $DB->get_record('course', ['shortname' => $params['shortname_clone']]);
            if (!$source_course) {
                return [
                    'status' => 'error',
                    'id' => 0,
                    'message' => 'Không tìm thấy môn học với shortname: ' . $params['shortname_clone']
                ];
            }

            // Step 3: Check if new shortname already exists.
            if ($DB->record_exists('course', ['shortname' => $params['shortname']])) {
                return [
                    'status' => 'error', 
                    'id' => 0,
                    'message' => 'Shortname đã tồn tại: ' . $params['shortname']
                ];
            }

            // Step 4: Create new course with basic information from source course
            $result = self::create_course_copy($source_course, $params);
            
            return $result;

        } catch (Exception $e) {
            return [
                'status' => 'error',
                'id' => 0,
                'message' => 'Copy môn học thất bại: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Create a new course based on source course information.
     *
     * @param object $source_course Source course object
     * @param array $params New course parameters
     * @return array Result array
     */
    private static function create_course_copy($source_course, $params) {
        global $DB;
        
        try {
            // Create new course data based on source course
            $course_data = new stdClass();
            
            // Basic required fields
            $course_data->fullname = $params['fullname'];
            $course_data->shortname = $params['shortname'];
            $course_data->category = $source_course->category; // Keep same category
            $course_data->startdate = $params['startdate'];
            $course_data->enddate = $params['enddate'];
            
            // Copy other attributes from source course
            $course_data->visible = $source_course->visible;
            $course_data->format = $source_course->format;
            $course_data->showgrades = $source_course->showgrades;
            $course_data->newsitems = $source_course->newsitems;
            $course_data->maxbytes = $source_course->maxbytes;
            $course_data->showreports = $source_course->showreports;
            $course_data->groupmode = $source_course->groupmode;
            $course_data->groupmodeforce = $source_course->groupmodeforce;
            $course_data->defaultgroupingid = 0; // Reset grouping
            $course_data->enablecompletion = $source_course->enablecompletion;
            $course_data->completionnotify = $source_course->completionnotify;
            
            // Copy summary and format
            if (isset($source_course->summary)) {
                $course_data->summary = $source_course->summary;
                $course_data->summaryformat = $source_course->summaryformat;
            }
            
            // Course format specific settings
            if ($source_course->format == 'topics' && isset($source_course->numsections)) {
                $course_data->numsections = $source_course->numsections;
            }
            
            // Create the new course
            $new_course = create_course($course_data);
            
            if (!$new_course) {
                return [
                    'status' => 'error',
                    'id' => 0,
                    'message' => 'Không thể tạo môn học mới'
                ];
            }

            // Copy course format options
            self::copy_course_format_options($source_course->id, $new_course->id);

            return [
                'status' => 'success',
                'id' => $new_course->id,
                'message' => 'Copy môn học thành công! (Đã tạo môn học mới với cấu trúc cơ bản từ môn học gốc)'
            ];
            
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'id' => 0,
                'message' => 'Lỗi tạo môn học: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Copy course format options from source to target course.
     *
     * @param int $source_courseid Source course ID
     * @param int $target_courseid Target course ID
     */
    private static function copy_course_format_options($source_courseid, $target_courseid) {
        global $DB;
        
        try {
            // Get course format options from source course
            $format_options = $DB->get_records('course_format_options', ['courseid' => $source_courseid]);
            
            foreach ($format_options as $option) {
                $new_option = new stdClass();
                $new_option->courseid = $target_courseid;
                $new_option->format = $option->format;
                $new_option->sectionid = 0; // For course-level options
                $new_option->name = $option->name;
                $new_option->value = $option->value;
                
                $DB->insert_record('course_format_options', $new_option);
            }
        } catch (Exception $e) {
            // Log error but don't fail the main operation
            error_log('Failed to copy course format options: ' . $e->getMessage());
        }
    }

    /**
     * Validate clone parameters.
     *
     * @param array $params Parameters to validate
     * @return array Validation result
     */
    private static function validate_clone_parameters($params) {
        // Validate dates.
        if (!is_numeric($params['startdate']) || !is_numeric($params['enddate'])) {
            return [
                'success' => false,
                'message' => 'Ngày không hợp lệ'
            ];
        }

        if ($params['enddate'] <= $params['startdate']) {
            return [
                'success' => false,
                'message' => 'Ngày kết thúc phải sau ngày bắt đầu'
            ];
        }

        // Validate required fields.
        if (empty(trim($params['fullname'])) || empty(trim($params['shortname']))) {
            return [
                'success' => false,
                'message' => 'Tên đầy đủ và tên viết tắt không được để trống'
            ];
        }

        if (empty(trim($params['shortname_clone']))) {
            return [
                'success' => false,
                'message' => 'Shortname của môn học nguồn không được để trống'
            ];
        }

        return ['success' => true];
    }

    /**
     * Describes the return value for clone_course function.
     *
     * @return external_single_structure
     */
    public static function clone_course_returns() {
        return new external_single_structure([
            'status' => new external_value(PARAM_TEXT, 'Trạng thái: success hoặc error'),
            'id' => new external_value(PARAM_INT, 'ID của môn học đã copy (0 nếu có lỗi)'),
            'message' => new external_value(PARAM_TEXT, 'Thông báo thành công hoặc mô tả lỗi'),
        ]);
    }

    /**
     * Describes the parameters for get_course_list function.
     *
     * @return external_function_parameters
     */
    public static function get_course_list_parameters() {
        return new external_function_parameters([
            'categoryid' => new external_value(PARAM_INT, 'ID danh mục (0 = tất cả)', VALUE_DEFAULT, 0),
            'visible' => new external_value(PARAM_BOOL, 'Chỉ lấy course hiển thị', VALUE_DEFAULT, true),
        ]);
    }

    /**
     * Get list of courses for cloning.
     *
     * @param int $categoryid Category ID (0 for all)
     * @param bool $visible Only visible courses
     * @return array List of courses
     */
    public static function get_course_list($categoryid = 0, $visible = true) {
        global $DB;

        // Validate parameters.
        $params = self::validate_parameters(self::get_course_list_parameters(), [
            'categoryid' => $categoryid,
            'visible' => $visible,
        ]);

        // Validate context and capabilities.
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('moodle/course:view', $context);

        try {
            $conditions = ['id' => '> 1']; // Exclude site course
            
            if ($params['categoryid'] > 0) {
                $conditions['category'] = $params['categoryid'];
            }
            
            if ($params['visible']) {
                $conditions['visible'] = 1;
            }

            $courses = $DB->get_records('course', $conditions, 'fullname ASC', 
                'id, fullname, shortname, category, startdate, enddate, visible');

            $result = [];
            foreach ($courses as $course) {
                $result[] = [
                    'id' => $course->id,
                    'fullname' => $course->fullname,
                    'shortname' => $course->shortname,
                    'category' => $course->category,
                    'startdate' => $course->startdate,
                    'enddate' => $course->enddate,
                    'visible' => $course->visible,
                ];
            }

            return [
                'courses' => $result,
                'total' => count($result),
            ];

        } catch (Exception $e) {
            throw new moodle_exception('error', 'local_courseclone', '', $e->getMessage());
        }
    }

    /**
     * Describes the return value for get_course_list function.
     *
     * @return external_single_structure
     */
    public static function get_course_list_returns() {
        return new external_single_structure([
            'courses' => new external_multiple_structure(
                new external_single_structure([
                    'id' => new external_value(PARAM_INT, 'Course ID'),
                    'fullname' => new external_value(PARAM_TEXT, 'Course full name'),
                    'shortname' => new external_value(PARAM_TEXT, 'Course short name'),
                    'category' => new external_value(PARAM_INT, 'Category ID'),
                    'startdate' => new external_value(PARAM_INT, 'Start date timestamp'),
                    'enddate' => new external_value(PARAM_INT, 'End date timestamp'),
                    'visible' => new external_value(PARAM_BOOL, 'Is visible'),
                ])
            ),
            'total' => new external_value(PARAM_INT, 'Total number of courses'),
        ]);
    }

    /**
     * Describes the parameters for get_clone_status function.
     *
     * @return external_function_parameters
     */
    public static function get_clone_status_parameters() {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'Course ID to check'),
        ]);
    }

    /**
     * Get clone status and information of a course.
     *
     * @param int $courseid Course ID
     * @return array Course information
     */
    public static function get_clone_status($courseid) {
        global $DB;

        // Validate parameters.
        $params = self::validate_parameters(self::get_clone_status_parameters(), [
            'courseid' => $courseid,
        ]);

        // Validate context and capabilities.
        $context = context_course::instance($params['courseid']);
        self::validate_context($context);
        require_capability('moodle/course:view', $context);

        try {
            $course = $DB->get_record('course', ['id' => $params['courseid']], '*', MUST_EXIST);

            // Get course statistics
            $enrolled_count = $DB->count_records_sql(
                "SELECT COUNT(DISTINCT u.id) 
                 FROM {user} u 
                 JOIN {user_enrolments} ue ON u.id = ue.userid 
                 JOIN {enrol} e ON ue.enrolid = e.id 
                 WHERE e.courseid = ? AND u.deleted = 0",
                [$params['courseid']]
            );

            $sections_count = $DB->count_records('course_sections', ['course' => $params['courseid']]);
            $activities_count = $DB->count_records('course_modules', ['course' => $params['courseid']]);

            return [
                'id' => $course->id,
                'fullname' => $course->fullname,
                'shortname' => $course->shortname,
                'category' => $course->category,
                'startdate' => $course->startdate,
                'enddate' => $course->enddate,
                'visible' => $course->visible,
                'format' => $course->format,
                'enrolled_users' => $enrolled_count,
                'sections_count' => $sections_count,
                'activities_count' => $activities_count,
                'can_clone' => true,
                'status' => 'ready_for_clone',
            ];

        } catch (Exception $e) {
            throw new moodle_exception('coursenotfound', 'local_courseclone');
        }
    }

    /**
     * Describes the return value for get_clone_status function.
     *
     * @return external_single_structure
     */
    public static function get_clone_status_returns() {
        return new external_single_structure([
            'id' => new external_value(PARAM_INT, 'Course ID'),
            'fullname' => new external_value(PARAM_TEXT, 'Course full name'),
            'shortname' => new external_value(PARAM_TEXT, 'Course short name'),
            'category' => new external_value(PARAM_INT, 'Category ID'),
            'startdate' => new external_value(PARAM_INT, 'Start date timestamp'),
            'enddate' => new external_value(PARAM_INT, 'End date timestamp'),
            'visible' => new external_value(PARAM_BOOL, 'Is visible'),
            'format' => new external_value(PARAM_TEXT, 'Course format'),
            'enrolled_users' => new external_value(PARAM_INT, 'Number of enrolled users'),
            'sections_count' => new external_value(PARAM_INT, 'Number of sections'),
            'activities_count' => new external_value(PARAM_INT, 'Number of activities'),
            'can_clone' => new external_value(PARAM_BOOL, 'Can be cloned'),
            'status' => new external_value(PARAM_TEXT, 'Clone status'),
        ]);
    }
}