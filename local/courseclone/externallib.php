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
 * @copyright  2025 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
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
            'shortname_clone' => new external_value(PARAM_TEXT, 'Shortname of the source course to clone'),
            'fullname' => new external_value(PARAM_TEXT, 'Full name for the new course'),
            'shortname' => new external_value(PARAM_TEXT, 'Short name for the new course'),
            'startdate' => new external_value(PARAM_INT, 'Start date timestamp for the new course'),
            'enddate' => new external_value(PARAM_INT, 'End date timestamp for the new course'),
        ]);
    }

    /**
     * Clone a course with new details.
     *
     * @param string $shortname_clone Shortname of source course
     * @param string $fullname Full name for new course
     * @param string $shortname Short name for new course  
     * @param int $startdate Start date timestamp
     * @param int $enddate End date timestamp
     * @return array Result with status, id, and message
     */
    public static function clone_course($shortname_clone, $fullname, $shortname, $startdate, $enddate) {
        global $DB, $USER;

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
        require_capability('moodle/backup:backupcourse', $context);
        require_capability('moodle/restore:restorecourse', $context);

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
                    'message' => get_string('error_coursenotfound', 'local_courseclone', $params['shortname_clone'])
                ];
            }

            // Step 3: Check if new shortname already exists.
            if ($DB->record_exists('course', ['shortname' => $params['shortname']])) {
                return [
                    'status' => 'error', 
                    'id' => 0,
                    'message' => get_string('error_shortnameinuse', 'local_courseclone', $params['shortname'])
                ];
            }

            // Step 4: Create backup of source course.
            $backup_result = self::create_course_backup($source_course->id);
            if (!$backup_result['success']) {
                return [
                    'status' => 'error',
                    'id' => 0,
                    'message' => $backup_result['message']
                ];
            }

            // Step 5: Create new course.
            $new_course = self::create_new_course($params);
            if (!$new_course) {
                return [
                    'status' => 'error',
                    'id' => 0,
                    'message' => get_string('error_createcoursefailed', 'local_courseclone')
                ];
            }

            // Step 6: Restore backup to new course.
            $restore_result = self::restore_course_backup($backup_result['backup_path'], $new_course->id);
            if (!$restore_result['success']) {
                // Delete the created course if restore fails.
                delete_course($new_course->id, false);
                return [
                    'status' => 'error',
                    'id' => 0,
                    'message' => $restore_result['message']
                ];
            }

            // Step 7: Update course dates.
            self::update_course_dates($new_course->id, $params['startdate'], $params['enddate']);

            // Step 8: Clean up backup file.
            if (isset($backup_result['backup_path']) && file_exists($backup_result['backup_path'])) {
                unlink($backup_result['backup_path']);
            }

            return [
                'status' => 'success',
                'id' => $new_course->id,
                'message' => get_string('success_coursecloned', 'local_courseclone')
            ];

        } catch (Exception $e) {
            return [
                'status' => 'error',
                'id' => 0,
                'message' => get_string('error_cloningfailed', 'local_courseclone') . ': ' . $e->getMessage()
            ];
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
                'message' => get_string('error_invaliddate', 'local_courseclone')
            ];
        }

        if ($params['enddate'] <= $params['startdate']) {
            return [
                'success' => false,
                'message' => get_string('error_enddatebeforestartdate', 'local_courseclone')
            ];
        }

        // Validate required fields.
        if (empty(trim($params['fullname'])) || empty(trim($params['shortname']))) {
            return [
                'success' => false,
                'message' => get_string('error_invalidparameter', 'local_courseclone')
            ];
        }

        return ['success' => true];
    }

    /**
     * Create course backup.
     *
     * @param int $courseid Course ID to backup
     * @return array Backup result
     */
    private static function create_course_backup($courseid) {
        global $CFG, $USER;

        try {
            // Create backup controller.
            $bc = new backup_controller(
                backup::TYPE_1COURSE,
                $courseid,
                backup::FORMAT_MOODLE,
                backup::INTERACTIVE_NO,
                backup::MODE_GENERAL,
                $USER->id
            );

            // Execute backup.
            $bc->execute_plan();
            $backup_results = $bc->get_results();
            $backup_file = $backup_results['backup_destination'];
            
            // Get backup file path.
            $backup_path = $backup_file->copy_content_to_temp();
            
            // Destroy backup controller.
            $bc->destroy();

            return [
                'success' => true,
                'backup_path' => $backup_path,
                'message' => get_string('backup_completed', 'local_courseclone')
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => get_string('error_backupfailed', 'local_courseclone') . ': ' . $e->getMessage()
            ];
        }
    }

    /**
     * Create new course.
     *
     * @param array $params Course parameters
     * @return object|false New course object or false on failure
     */
    private static function create_new_course($params) {
        global $DB;

        try {
            // Get default category.
            $category = $DB->get_record('course_categories', ['id' => 1]);
            if (!$category) {
                $category = core_course_category::get_default();
            }

            // Prepare course data.
            $course_data = new stdClass();
            $course_data->fullname = $params['fullname'];
            $course_data->shortname = $params['shortname'];
            $course_data->category = $category->id;
            $course_data->startdate = $params['startdate'];
            $course_data->enddate = $params['enddate'];
            $course_data->visible = 1;
            $course_data->format = 'topics';
            $course_data->numsections = 10;

            // Create course.
            $new_course = create_course($course_data);
            
            return $new_course;

        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Restore course backup to new course.
     *
     * @param string $backup_path Path to backup file
     * @param int $courseid Target course ID
     * @return array Restore result
     */
    private static function restore_course_backup($backup_path, $courseid) {
        global $USER;

        try {
            // Create restore controller.
            $rc = new restore_controller(
                $backup_path,
                $courseid,
                backup::INTERACTIVE_NO,
                backup::MODE_GENERAL,
                $USER->id,
                backup::TARGET_EXISTING_DELETING
            );

            // Execute restore.
            if ($rc->execute_precheck()) {
                $rc->execute_plan();
                $rc->destroy();

                return [
                    'success' => true,
                    'message' => get_string('restore_completed', 'local_courseclone')
                ];
            } else {
                $rc->destroy();
                return [
                    'success' => false,
                    'message' => get_string('error_restorefailed', 'local_courseclone') . ': Precheck failed'
                ];
            }

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => get_string('error_restorefailed', 'local_courseclone') . ': ' . $e->getMessage()
            ];
        }
    }

    /**
     * Update course dates after restore.
     *
     * @param int $courseid Course ID
     * @param int $startdate Start date timestamp
     * @param int $enddate End date timestamp
     */
    private static function update_course_dates($courseid, $startdate, $enddate) {
        global $DB;

        $update_data = new stdClass();
        $update_data->id = $courseid;
        $update_data->startdate = $startdate;
        $update_data->enddate = $enddate;
        $update_data->timemodified = time();

        $DB->update_record('course', $update_data);
    }

    /**
     * Describes the return value for clone_course function.
     *
     * @return external_single_structure
     */
    public static function clone_course_returns() {
        return new external_single_structure([
            'status' => new external_value(PARAM_TEXT, 'Status: success or error'),
            'id' => new external_value(PARAM_INT, 'ID of the cloned course (0 if error)'),
            'message' => new external_value(PARAM_TEXT, 'Success message or error description'),
        ]);
    }
}