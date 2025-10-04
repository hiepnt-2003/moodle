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
 * External API for course copy using RESTful webservice protocol.
 *
 * @package    local_coursecopy
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');

/**
 * Course copy external functions for RESTful webservice.
 */
class local_coursecopy_external extends external_api {

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     */
    public static function copy_course_parameters() {
        return new external_function_parameters([
            'shortname_clone' => new external_value(PARAM_TEXT, 'Short name of the source course to clone'),
            'fullname' => new external_value(PARAM_TEXT, 'Full name for the new course'),
            'shortname' => new external_value(PARAM_TEXT, 'Short name for the new course'),
            'startdate' => new external_value(PARAM_INT, 'Start date timestamp for the new course'),
            'enddate' => new external_value(PARAM_INT, 'End date timestamp for the new course'),
        ]);
    }

    /**
     * Copy a course.
     *
     * @param string $shortname_clone Short name of source course
     * @param string $fullname Full name for new course
     * @param string $shortname Short name for new course
     * @param int $startdate Start date timestamp
     * @param int $enddate End date timestamp
     * @return array Result array
     */
    public static function copy_course($shortname_clone, $fullname, $shortname, $startdate, $enddate) {
        global $DB, $USER;

        // Validate parameters.
        $params = self::validate_parameters(self::copy_course_parameters(), [
            'shortname_clone' => $shortname_clone,
            'fullname' => $fullname,
            'shortname' => $shortname,
            'startdate' => $startdate,
            'enddate' => $enddate,
        ]);

        // Validate context.
        $context = context_system::instance();
        self::validate_context($context);

        // Check capability.
        require_capability('moodle/course:create', $context);
        require_capability('moodle/backup:backupcourse', $context);
        require_capability('moodle/restore:restorecourse', $context);

        try {
            // Get source course.
            $sourcecourse = $DB->get_record('course', ['shortname' => $params['shortname_clone']]);
            if (!$sourcecourse) {
                return [
                    'status' => 'error',
                    'id' => 0,
                    'message' => 'Source course not found with shortname: ' . $params['shortname_clone'],
                ];
            }

            // Check if target shortname already exists.
            if ($DB->record_exists('course', ['shortname' => $params['shortname']])) {
                return [
                    'status' => 'error',
                    'id' => 0,
                    'message' => 'Course with shortname "' . $params['shortname'] . '" already exists',
                ];
            }

            // Validate dates.
            if ($params['startdate'] >= $params['enddate']) {
                return [
                    'status' => 'error',
                    'id' => 0,
                    'message' => 'Start date must be before end date',
                ];
            }

            // Check user can access source course.
            $coursecontext = context_course::instance($sourcecourse->id);
            if (!has_capability('moodle/backup:backupcourse', $coursecontext)) {
                return [
                    'status' => 'error',
                    'id' => 0,
                    'message' => 'User does not have permission to backup the source course',
                ];
            }

            // Create new course.
            $coursedata = new stdClass();
            $coursedata->category = $sourcecourse->category;
            $coursedata->fullname = $params['fullname'];
            $coursedata->shortname = $params['shortname'];
            $coursedata->startdate = $params['startdate'];
            $coursedata->enddate = $params['enddate'];
            $coursedata->format = $sourcecourse->format;
            $coursedata->visible = $sourcecourse->visible;
            $coursedata->summary = $sourcecourse->summary;
            $coursedata->summaryformat = $sourcecourse->summaryformat;

            $newcourse = create_course($coursedata);

            // Perform backup and restore.
            $bc = new backup_controller(
                backup::TYPE_1COURSE,
                $sourcecourse->id,
                backup::FORMAT_MOODLE,
                backup::INTERACTIVE_NO,
                backup::MODE_GENERAL,
                $USER->id
            );

            $bc->execute_plan();
            $results = $bc->get_results();
            $file = $results['backup_destination'];
            $bc->destroy();

            // Restore to new course.
            $rc = new restore_controller(
                $file->get_filename(),
                $newcourse->id,
                backup::INTERACTIVE_NO,
                backup::MODE_GENERAL,
                $USER->id,
                backup::TARGET_NEW_COURSE
            );

            $rc->execute_precheck();
            $rc->execute_plan();
            $rc->destroy();

            // Clean up backup file.
            $file->delete();

            return [
                'status' => 'success',
                'id' => $newcourse->id,
                'message' => 'Course copied successfully',
            ];

        } catch (Exception $e) {
            return [
                'status' => 'error',
                'id' => 0,
                'message' => 'Error copying course: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Returns description of method result value.
     *
     * @return external_description
     */
    public static function copy_course_returns() {
        return new external_single_structure([
            'status' => new external_value(PARAM_TEXT, 'Status of the operation (success/error)'),
            'id' => new external_value(PARAM_INT, 'ID of the new course (0 if error)'),
            'message' => new external_value(PARAM_TEXT, 'Success or error message'),
        ]);
    }
}
