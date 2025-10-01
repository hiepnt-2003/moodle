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
 * English strings for local_courseclone plugin.
 *
 * @package    local_courseclone
 * @copyright  2025 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Course Clone Service';
$string['courseclone:clone'] = 'Clone courses via webservice';

// Success messages
$string['success_coursecloned'] = 'Course cloned successfully';
$string['backup_completed'] = 'Course backup completed successfully';
$string['restore_completed'] = 'Course restore completed successfully';

// Error messages
$string['error_coursenotfound'] = 'Source course with shortname "{$a}" not found';
$string['error_shortnameinuse'] = 'Course with shortname "{$a}" already exists';
$string['error_invaliddate'] = 'Invalid date format. Please provide valid timestamps';
$string['error_enddatebeforestartdate'] = 'End date must be after start date';
$string['error_invalidparameter'] = 'Required parameter is missing or invalid';
$string['error_createcoursefailed'] = 'Failed to create new course';
$string['error_backupfailed'] = 'Course backup failed';
$string['error_restorefailed'] = 'Course restore failed';
$string['error_cloningfailed'] = 'Course cloning failed';

// Description
$string['privacy:metadata'] = 'The Course Clone plugin does not store any personal data.';