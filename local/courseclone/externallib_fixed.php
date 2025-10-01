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
 * External web service functions for course cloning - FIXED VERSION.
 *
 * @package    local_courseclone
 * @copyright  2025 Your Name <your.email@example.com>
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
            'shortname_clone' => new external_value(PARAM_TEXT, 'Shortname of the source course to clone'),
            'fullname' => new external_value(PARAM_TEXT, 'Full name for the new course'),
            'shortname' => new external_value(PARAM_TEXT, 'Short name for the new course'),
            'startdate' => new external_value(PARAM_INT, 'Start date timestamp for the new course'),
            'enddate' => new external_value(PARAM_INT, 'End date timestamp for the new course'),
        ]);
    }

    /**
     * Clone a course with new details using Moodle's duplicate_course function.
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
                    'message' => 'Source course with shortname "' . $params['shortname_clone'] . '" not found'
                ];
            }

            // Step 3: Check if new shortname already exists.
            if ($DB->record_exists('course', ['shortname' => $params['shortname']])) {
                return [
                    'status' => 'error', 
                    'id' => 0,
                    'message' => 'Course with shortname "' . $params['shortname'] . '" already exists'
                ];
            }

            // Step 4: Use Moodle's built-in duplicate_course function.
            require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
            require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');

            // Create course data for the new course
            $course_data = [
                'fullname' => $params['fullname'],
                'shortname' => $params['shortname'],
                'categoryid' => $source_course->category,
                'visible' => 1,
                'startdate' => $params['startdate'],
                'enddate' => $params['enddate']
            ];

            // Use Moodle's duplicate_course function
            $new_course = duplicate_course($source_course->id, $course_data['fullname'], 
                                        $course_data['shortname'], $course_data['categoryid'], 
                                        $course_data['visible'], [
                                            'users' => 0,
                                            'role_assignments' => 0,
                                            'activities' => 1,
                                            'blocks' => 1,
                                            'filters' => 1,
                                            'comments' => 0,
                                            'completion_information' => 0,
                                            'logs' => 0,
                                            'grade_histories' => 0
                                        ]);

            if (!$new_course) {
                return [
                    'status' => 'error',
                    'id' => 0,
                    'message' => 'Failed to duplicate course'
                ];
            }

            // Step 5: Update course dates.
            self::update_course_dates($new_course->id, $params['startdate'], $params['enddate']);

            return [
                'status' => 'success',
                'id' => $new_course->id,
                'message' => 'Course cloned successfully'
            ];

        } catch (Exception $e) {
            return [
                'status' => 'error',
                'id' => 0,
                'message' => 'Course cloning failed: ' . $e->getMessage()
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
                'message' => 'Invalid date format. Please provide valid timestamps'
            ];
        }

        if ($params['enddate'] <= $params['startdate']) {
            return [
                'success' => false,
                'message' => 'End date must be after start date'
            ];
        }

        // Validate required fields.
        if (empty(trim($params['fullname'])) || empty(trim($params['shortname']))) {
            return [
                'success' => false,
                'message' => 'Required parameter fullname or shortname is missing or invalid'
            ];
        }

        return ['success' => true];
    }

    /**
     * Update course dates after cloning.
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