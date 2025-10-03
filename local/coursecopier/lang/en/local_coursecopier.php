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
 * Language strings for Course Copier plugin.
 *
 * @package    local_coursecopier
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Course Copier';
$string['coursecopier:copyourse'] = 'Copy courses';
$string['coursecopier:viewcourses'] = 'View available courses for copying';

// Error messages
$string['error_sourcenotfound'] = 'Source course not found';
$string['error_targetexists'] = 'Target course shortname already exists';
$string['error_invaliddate'] = 'Invalid date format';
$string['error_enddatebeforestartdate'] = 'End date must be after start date';
$string['error_emptyfields'] = 'Required fields cannot be empty';
$string['error_backupfailed'] = 'Course backup failed';
$string['error_restorefailed'] = 'Course restore failed';

// Success messages
$string['success_coursecreated'] = 'Course copied successfully';
$string['success_coursesretrieved'] = 'Available courses retrieved successfully';

// API descriptions
$string['copy_course_desc'] = 'Copy a course with new details including shortname, fullname, start date and end date';
$string['get_available_courses_desc'] = 'Get list of courses available for copying';

// Parameter descriptions
$string['param_shortname_clone'] = 'Shortname of the source course to copy';
$string['param_fullname'] = 'Full name for the new course';
$string['param_shortname'] = 'Short name for the new course';
$string['param_startdate'] = 'Start date timestamp for the new course';
$string['param_enddate'] = 'End date timestamp for the new course';
$string['param_categoryid'] = 'Category ID (0 for all categories)';