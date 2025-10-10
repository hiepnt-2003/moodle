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
 * External web service functions for API Services (Course Copy & User Creation).
 *
 * @package    local_apiservices
 * @copyright  2025 API Services Team
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/user/lib.php');
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');

/**
 * External functions for API Services.
 */
class local_apiservices_external extends external_api {

    // ==========================================
    // COURSE COPY FUNCTIONS
    // ==========================================

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

            // Step 3: Check if new shortname already exists.
            if ($DB->record_exists('course', ['shortname' => $params['shortname']])) {
                return [
                    'status' => 'error', 
                    'id' => 0,
                    'message' => 'Shortname đã tồn tại: ' . $params['shortname']
                ];
            }

            // Step 4: Create new course copy with full content
            $result = self::create_course_copy_with_content($source_course, $params);
            
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
     * Create a new course copy with full content using Backup/Restore API.
     *
     * @param object $source_course Source course object
     * @param array $params New course parameters
     * @return array Result array
     */
    private static function create_course_copy_with_content($source_course, $params) {
        global $DB, $USER, $CFG;
        
        try {
            // Step 1: Create empty target course first
            $course_data = new stdClass();
            $course_data->fullname = $params['fullname'];
            $course_data->shortname = $params['shortname'];
            $course_data->category = $source_course->category;
            $course_data->visible = $source_course->visible;
            $course_data->startdate = $params['startdate'];
            $course_data->enddate = $params['enddate'];
            
            $new_course = create_course($course_data);
            
            if (!$new_course) {
                return [
                    'status' => 'error',
                    'id' => 0,
                    'message' => 'Không thể tạo môn học mới'
                ];
            }

            // Step 2: Use backup and restore to copy all content
            $admin = get_admin();
            if (!$admin) {
                return [
                    'status' => 'error',
                    'id' => $new_course->id,
                    'message' => 'Không tìm thấy admin user'
                ];
            }

            // Create backup
            $bc = new backup_controller(
                backup::TYPE_1COURSE,
                $source_course->id,
                backup::FORMAT_MOODLE,
                backup::INTERACTIVE_NO,
                backup::MODE_GENERAL,
                $admin->id
            );
            
            // Execute backup with default settings
            $bc->execute_plan();
            $results = $bc->get_results();
            $backup_file = $results['backup_destination'];
            
            if (!$backup_file) {
                $bc->destroy();
                return [
                    'status' => 'error',
                    'id' => $new_course->id,
                    'message' => 'Không thể tạo file backup'
                ];
            }

            // Get backup file path
            $backup_filepath = $backup_file->copy_content_to_temp();
            $bc->destroy();

            // Step 3: Restore to new course
            $rc = new restore_controller(
                basename($backup_filepath),
                $new_course->id,
                backup::INTERACTIVE_NO,
                backup::MODE_GENERAL,
                $admin->id,
                backup::TARGET_CURRENT_ADDING
            );

            // Execute restore with default settings
            if (!$rc->execute_precheck()) {
                $precheckresults = $rc->get_precheck_results();
                $rc->destroy();
                
                return [
                    'status' => 'error',
                    'id' => $new_course->id,
                    'message' => 'Precheck thất bại: ' . implode(', ', $precheckresults)
                ];
            }

            $rc->execute_plan();
            $rc->destroy();

            // Clean up temp backup file
            if (file_exists($backup_filepath)) {
                @unlink($backup_filepath);
            }

            // Step 4: Update course dates
            $new_course->startdate = $params['startdate'];
            $new_course->enddate = $params['enddate'];
            $new_course->fullname = $params['fullname'];
            $new_course->shortname = $params['shortname'];
            $DB->update_record('course', $new_course);

            return [
                'status' => 'success',
                'id' => $new_course->id,
                'message' => 'Copy đầy đủ nội dung môn học thành công! ID môn học mới: ' . $new_course->id
            ];
            
        } catch (Exception $e) {
            // If there's an error and we created a course, try to delete it
            if (isset($new_course) && $new_course->id) {
                try {
                    delete_course($new_course->id, false);
                } catch (Exception $delete_error) {
                    // Ignore deletion errors
                }
            }
            
            return [
                'status' => 'error',
                'id' => 0,
                'message' => 'Lỗi khi copy nội dung môn học: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Create a new course based on source course information (Legacy - without content).
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
            
            // Basic required fields from parameters
            $course_data->fullname = $params['fullname'];
            $course_data->shortname = $params['shortname'];
            $course_data->startdate = $params['startdate'];
            $course_data->enddate = $params['enddate'];
            
            // Copy attributes from source course
            $course_data->category = $source_course->category;
            $course_data->visible = $source_course->visible;
            $course_data->format = $source_course->format;
            $course_data->showgrades = $source_course->showgrades;
            $course_data->newsitems = $source_course->newsitems;
            $course_data->maxbytes = $source_course->maxbytes;
            $course_data->showreports = $source_course->showreports;
            $course_data->groupmode = $source_course->groupmode;
            $course_data->groupmodeforce = $source_course->groupmodeforce;
            $course_data->defaultgroupingid = 0;
            $course_data->enablecompletion = $source_course->enablecompletion;
            $course_data->completionnotify = $source_course->completionnotify;
            
            // Copy summary
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
                'message' => 'Copy môn học thành công! ID môn học mới: ' . $new_course->id
            ];
            
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'id' => 0,
                'message' => 'Lỗi khi tạo môn học: ' . $e->getMessage()
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
                $new_option->sectionid = 0;
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
     * Validate copy parameters.
     *
     * @param array $params Parameters to validate
     * @return array Validation result
     */
    private static function validate_copy_parameters($params) {
        // Validate dates
        if (!is_numeric($params['startdate']) || !is_numeric($params['enddate'])) {
            return [
                'success' => false,
                'message' => 'Ngày không hợp lệ. Vui lòng sử dụng định dạng timestamp'
            ];
        }

        if ($params['startdate'] < 0 || $params['enddate'] < 0) {
            return [
                'success' => false,
                'message' => 'Ngày phải là số nguyên dương (timestamp)'
            ];
        }

        if ($params['enddate'] <= $params['startdate']) {
            return [
                'success' => false,
                'message' => 'Ngày kết thúc phải sau ngày bắt đầu'
            ];
        }

        // Validate required fields
        if (empty(trim($params['fullname']))) {
            return [
                'success' => false,
                'message' => 'Tên đầy đủ không được để trống'
            ];
        }

        if (empty(trim($params['shortname']))) {
            return [
                'success' => false,
                'message' => 'Tên viết tắt không được để trống'
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

    // ==========================================
    // USER CREATION FUNCTIONS
    // ==========================================

    /**
     * Describes the parameters for create_user function.
     *
     * @return external_function_parameters
     */
    public static function create_user_parameters() {
        return new external_function_parameters([
            'username' => new external_value(PARAM_USERNAME, 'Username for the new user'),
            'firstname' => new external_value(PARAM_TEXT, 'First name of the user'),
            'lastname' => new external_value(PARAM_TEXT, 'Last name of the user'),
            'email' => new external_value(PARAM_EMAIL, 'Email address of the user'),
            'createpassword' => new external_value(PARAM_BOOL, 'Whether to create a password automatically'),
            'password' => new external_value(PARAM_TEXT, 'Password for the user (required if createpassword is false)', VALUE_DEFAULT, ''),
        ]);
    }

    /**
     * Create a new user.
     *
     * @param string $username Username
     * @param string $firstname First name
     * @param string $lastname Last name
     * @param string $email Email address
     * @param bool $createpassword Whether to create password automatically
     * @param string $password Password (if not auto-generating)
     * @return array Result with status, id, and message
     */
    public static function create_user($username, $firstname, $lastname, $email, $createpassword, $password = '') {
        global $DB, $CFG;

        // Validate parameters.
        $params = self::validate_parameters(self::create_user_parameters(), [
            'username' => $username,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email,
            'createpassword' => $createpassword,
            'password' => $password,
        ]);

        // Validate context and capabilities.
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('moodle/user:create', $context);

        try {
            // Step 1: Validate input parameters.
            $validation_result = self::validate_user_parameters($params);
            if (!$validation_result['success']) {
                return [
                    'status' => 'error',
                    'id' => 0,
                    'message' => $validation_result['message']
                ];
            }

            // Step 2: Check if username already exists.
            if ($DB->record_exists('user', ['username' => $params['username']])) {
                return [
                    'status' => 'error',
                    'id' => 0,
                    'message' => get_string('error_usernameinuse', 'local_apiservices', $params['username'])
                ];
            }

            // Step 3: Check if email already exists.
            if ($DB->record_exists('user', ['email' => $params['email']])) {
                return [
                    'status' => 'error',
                    'id' => 0,
                    'message' => get_string('error_emailinuse', 'local_apiservices', $params['email'])
                ];
            }

            // Step 4: Prepare user data.
            $user = new stdClass();
            $user->username = $params['username'];
            $user->firstname = $params['firstname'];
            $user->lastname = $params['lastname'];
            $user->email = $params['email'];
            $user->confirmed = 1;
            $user->mnethostid = $CFG->mnet_localhost_id;
            $user->auth = 'manual';
            $user->timecreated = time();
            $user->timemodified = time();

            // Step 5: Handle password.
            if ($params['createpassword']) {
                // Generate random password.
                $user->password = hash_internal_user_password(self::generate_random_password());
            } else {
                if (empty($params['password'])) {
                    return [
                        'status' => 'error',
                        'id' => 0,
                        'message' => get_string('error_passwordrequired', 'local_apiservices')
                    ];
                }
                // Validate password strength.
                if (!self::validate_password($params['password'])) {
                    return [
                        'status' => 'error',
                        'id' => 0,
                        'message' => get_string('error_passwordweak', 'local_apiservices')
                    ];
                }
                $user->password = hash_internal_user_password($params['password']);
            }

            // Step 6: Create the user.
            $user->id = $DB->insert_record('user', $user);

            if (!$user->id) {
                return [
                    'status' => 'error',
                    'id' => 0,
                    'message' => get_string('error_usercreationfailed', 'local_apiservices')
                ];
            }

            // Step 7: Trigger user created event.
            \core\event\user_created::create_from_userid($user->id)->trigger();

            return [
                'status' => 'success',
                'id' => $user->id,
                'message' => get_string('success_usercreated', 'local_apiservices')
            ];

        } catch (Exception $e) {
            return [
                'status' => 'error',
                'id' => 0,
                'message' => 'User creation failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Validate user parameters.
     *
     * @param array $params Parameters to validate
     * @return array Validation result
     */
    private static function validate_user_parameters($params) {
        // Validate required fields.
        if (empty(trim($params['username'])) || empty(trim($params['firstname'])) || 
            empty(trim($params['lastname'])) || empty(trim($params['email']))) {
            return [
                'success' => false,
                'message' => get_string('error_requiredfields', 'local_apiservices')
            ];
        }

        // Validate username format.
        if (!preg_match('/^[a-zA-Z0-9._-]+$/', $params['username'])) {
            return [
                'success' => false,
                'message' => get_string('error_invalidusername', 'local_apiservices')
            ];
        }

        // Validate email format.
        if (!filter_var($params['email'], FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => get_string('error_invalidemail', 'local_apiservices')
            ];
        }

        return ['success' => true];
    }

    /**
     * Generate random password.
     *
     * @return string Random password
     */
    private static function generate_random_password() {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        $password = '';
        for ($i = 0; $i < 12; $i++) {
            $password .= $chars[rand(0, strlen($chars) - 1)];
        }
        return $password;
    }

    /**
     * Validate password strength.
     *
     * @param string $password Password to validate
     * @return bool True if password is strong enough
     */
    private static function validate_password($password) {
        // At least 8 characters.
        if (strlen($password) < 8) {
            return false;
        }

        // At least one lowercase letter.
        if (!preg_match('/[a-z]/', $password)) {
            return false;
        }

        // At least one uppercase letter.
        if (!preg_match('/[A-Z]/', $password)) {
            return false;
        }

        // At least one digit.
        if (!preg_match('/[0-9]/', $password)) {
            return false;
        }

        return true;
    }

    /**
     * Describes the return value for create_user function.
     *
     * @return external_single_structure
     */
    public static function create_user_returns() {
        return new external_single_structure([
            'status' => new external_value(PARAM_TEXT, 'Status: success or error'),
            'id' => new external_value(PARAM_INT, 'ID of the created user (0 if error)'),
            'message' => new external_value(PARAM_TEXT, 'Success message or error description'),
        ]);
    }
}
