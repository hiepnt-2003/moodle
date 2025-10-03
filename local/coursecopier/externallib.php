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
 * External web service functions for course copying.
 *
 * @package    local_coursecopier
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');

/**
 * External functions for course copying.
 */
class local_coursecopier_external extends external_api {

    /**
     * Describes the parameters for copy_course function.
     *
     * @return external_function_parameters
     */
    public static function copy_course_parameters() {
        return new external_function_parameters([
            'shortname_clone' => new external_value(PARAM_TEXT, 'Shortname của môn học nguồn cần copy'),
            'fullname' => new external_value(PARAM_TEXT, 'Tên đầy đủ cho môn học mới'),
            'shortname' => new external_value(PARAM_TEXT, 'Tên viết tắt cho môn học mới'),
            'startdate' => new external_value(PARAM_INT, 'Ngày bắt đầu (timestamp) cho môn học mới'),
            'enddate' => new external_value(PARAM_INT, 'Ngày kết thúc (timestamp) cho môn học mới'),
        ]);
    }

    /**
     * Copy a course with new details.
     *
     * @param string $shortname_clone Shortname của môn học nguồn
     * @param string $fullname Tên đầy đủ cho môn học mới
     * @param string $shortname Tên viết tắt cho môn học mới  
     * @param int $startdate Ngày bắt đầu timestamp
     * @param int $enddate Ngày kết thúc timestamp
     * @return array Kết quả với status, id, và message
     */
    public static function copy_course($shortname_clone, $fullname, $shortname, $startdate, $enddate) {
        global $DB, $USER, $CFG;

        // Validate parameters.
        $params = self::validate_parameters(self::copy_course_parameters(), [
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
        require_capability('moodle/backup:backupcourse', $context);
        require_capability('moodle/restore:restorecourse', $context);

        try {
            // Step 1: Validate input parameters.
            $validation_result = self::validate_copy_parameters($params);
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

            // Check capabilities on source course
            $source_context = context_course::instance($source_course->id);
            require_capability('moodle/backup:backupcourse', $source_context);

            // Step 3: Check if new shortname already exists.
            if ($DB->record_exists('course', ['shortname' => $params['shortname']])) {
                return [
                    'status' => 'error', 
                    'id' => 0,
                    'message' => 'Shortname đã tồn tại: ' . $params['shortname']
                ];
            }

            // Step 4: Create new course first
            $new_course_data = new stdClass();
            $new_course_data->fullname = $params['fullname'];
            $new_course_data->shortname = $params['shortname'];
            $new_course_data->category = $source_course->category;
            $new_course_data->startdate = $params['startdate'];
            $new_course_data->enddate = $params['enddate'];
            $new_course_data->visible = 1;
            $new_course_data->format = $source_course->format;
            
            $new_course = create_course($new_course_data);
            
            if (!$new_course) {
                return [
                    'status' => 'error',
                    'id' => 0,
                    'message' => 'Không thể tạo môn học mới'
                ];
            }

            // Step 5: Perform backup and restore to copy content
            $copy_result = self::perform_course_copy($source_course->id, $new_course->id);
            
            if (!$copy_result['success']) {
                // Clean up the created course if copy failed
                delete_course($new_course->id, false);
                return [
                    'status' => 'error',
                    'id' => 0,
                    'message' => $copy_result['message']
                ];
            }

            // Step 6: Update course dates after restore
            self::update_course_dates($new_course->id, $params['startdate'], $params['enddate']);

            return [
                'status' => 'success',
                'id' => $new_course->id,
                'message' => 'Copy môn học thành công! Đã sao chép toàn bộ nội dung từ môn học gốc.'
            ];
            
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'id' => 0,
                'message' => 'Copy môn học thất bại: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Perform course backup and restore to copy content.
     *
     * @param int $source_courseid Source course ID
     * @param int $target_courseid Target course ID
     * @return array Result array
     */
    private static function perform_course_copy($source_courseid, $target_courseid) {
        global $CFG, $USER, $DB;
        
        try {
            // Validate courses exist
            $source_course = $DB->get_record('course', ['id' => $source_courseid]);
            $target_course = $DB->get_record('course', ['id' => $target_courseid]);
            
            if (!$source_course || !$target_course) {
                return [
                    'success' => false,
                    'message' => 'Course không tồn tại'
                ];
            }

            // Step 1: Backup source course
            $bc = new backup_controller(backup::TYPE_1COURSE, $source_courseid, backup::FORMAT_MOODLE,
                backup::INTERACTIVE_NO, backup::MODE_GENERAL, $USER->id);
            
            // Check if backup controller was created successfully
            if (!$bc) {
                return [
                    'success' => false,
                    'message' => 'Không thể tạo backup controller'
                ];
            }
            
            $plan = $bc->get_plan();
            if (!$plan) {
                $bc->destroy();
                return [
                    'success' => false,
                    'message' => 'Không thể lấy backup plan'
                ];
            }
                
            // Configure backup settings safely
            $anonymize_setting = $plan->get_setting('anonymize');
            if ($anonymize_setting) {
                $anonymize_setting->set_value(false);
            }
            
            $users_setting = $plan->get_setting('users');
            if ($users_setting) {
                $users_setting->set_value(false); // Don't copy users
            }
            
            $roles_setting = $plan->get_setting('role_assignments');
            if ($roles_setting) {
                $roles_setting->set_value(false);
            }
            
            $bc->execute_plan();
            $backup_id = $bc->get_backupid();
            $bc->destroy();

            if (!$backup_id) {
                return [
                    'success' => false,
                    'message' => 'Backup thất bại: không có backup ID'
                ];
            }

            // Step 2: Restore to target course
            $rc = new restore_controller($backup_id, $target_courseid,
                backup::INTERACTIVE_NO, backup::MODE_GENERAL, $USER->id, backup::TARGET_EXISTING_DELETING);
            
            if (!$rc) {
                return [
                    'success' => false,
                    'message' => 'Không thể tạo restore controller'
                ];
            }
            
            $restore_plan = $rc->get_plan();
            if (!$restore_plan) {
                $rc->destroy();
                return [
                    'success' => false,
                    'message' => 'Không thể lấy restore plan'
                ];
            }
                
            // Configure restore settings safely
            $users_restore_setting = $restore_plan->get_setting('users');
            if ($users_restore_setting) {
                $users_restore_setting->set_value(false);
            }
            
            $roles_restore_setting = $restore_plan->get_setting('role_assignments');
            if ($roles_restore_setting) {
                $roles_restore_setting->set_value(false);
            }
            
            if (!$rc->execute_precheck()) {
                $rc->destroy();
                return [
                    'success' => false,
                    'message' => 'Precheck thất bại: không thể restore course'
                ];
            }
            
            $rc->execute_plan();
            $rc->destroy();

            return [
                'success' => true,
                'message' => 'Copy nội dung thành công'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Lỗi trong quá trình backup/restore: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Update course start and end dates.
     *
     * @param int $courseid Course ID
     * @param int $startdate Start date timestamp
     * @param int $enddate End date timestamp
     */
    private static function update_course_dates($courseid, $startdate, $enddate) {
        global $DB;
        
        $course = new stdClass();
        $course->id = $courseid;
        $course->startdate = $startdate;
        $course->enddate = $enddate;
        
        $DB->update_record('course', $course);
        
        // Rebuild course cache
        rebuild_course_cache($courseid, true);
    }

    /**
     * Validate copy parameters.
     *
     * @param array $params Parameters to validate
     * @return array Validation result
     */
    private static function validate_copy_parameters($params) {
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

        // Validate shortname format
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $params['shortname'])) {
            return [
                'success' => false,
                'message' => 'Shortname chỉ được chứa chữ cái, số, dấu gạch dưới và dấu gạch ngang'
            ];
        }

        return ['success' => true];
    }

    /**
     * Describes the return value for copy_course function.
     *
     * @return external_single_structure
     */
    public static function copy_course_returns() {
        return new external_single_structure([
            'status' => new external_value(PARAM_TEXT, 'Trạng thái: success hoặc error'),
            'id' => new external_value(PARAM_INT, 'ID của môn học đã copy (0 nếu có lỗi)'),
            'message' => new external_value(PARAM_TEXT, 'Thông báo thành công hoặc mô tả lỗi'),
        ]);
    }

    /**
     * Describes the parameters for get_available_courses function.
     *
     * @return external_function_parameters
     */
    public static function get_available_courses_parameters() {
        return new external_function_parameters([
            'categoryid' => new external_value(PARAM_INT, 'ID danh mục (0 = tất cả)', VALUE_DEFAULT, 0),
        ]);
    }

    /**
     * Get list of available courses for copying.
     *
     * @param int $categoryid Category ID (0 for all)
     * @return array List of courses
     */
    public static function get_available_courses($categoryid = 0) {
        global $DB;

        // Validate parameters.
        $params = self::validate_parameters(self::get_available_courses_parameters(), [
            'categoryid' => $categoryid,
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

            $courses = $DB->get_records('course', $conditions, 'fullname ASC', 
                'id, fullname, shortname, category, startdate, enddate, visible');

            $result = [];
            foreach ($courses as $course) {
                // Check if user can backup this course
                $course_context = context_course::instance($course->id);
                if (has_capability('moodle/backup:backupcourse', $course_context)) {
                    $result[] = [
                        'id' => $course->id,
                        'fullname' => $course->fullname,
                        'shortname' => $course->shortname,
                        'category' => $course->category,
                        'startdate' => $course->startdate,
                        'enddate' => $course->enddate,
                        'visible' => (bool)$course->visible,
                    ];
                }
            }

            return [
                'courses' => $result,
                'total' => count($result),
                'status' => 'success',
                'message' => 'Lấy danh sách môn học thành công'
            ];

        } catch (Exception $e) {
            return [
                'courses' => [],
                'total' => 0,
                'status' => 'error',
                'message' => 'Lỗi khi lấy danh sách môn học: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Describes the return value for get_available_courses function.
     *
     * @return external_single_structure
     */
    public static function get_available_courses_returns() {
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
            'status' => new external_value(PARAM_TEXT, 'Status of the operation'),
            'message' => new external_value(PARAM_TEXT, 'Status message'),
        ]);
    }
}