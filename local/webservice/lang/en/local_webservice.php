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
 * English language strings for local_webservice plugin.
 *
 * @package    local_webservice
 * @copyright  2025 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Course Clone Web Service';
$string['webservice:clonecourse'] = 'Clone course via web service';

// Error messages
$string['error_coursenotfound'] = 'Source course not found with shortname: {$a}';
$string['error_shortnameinuse'] = 'Course shortname already exists: {$a}';
$string['error_invaliddate'] = 'Invalid date format. Use timestamp format.';
$string['error_enddatebeforestartdate'] = 'End date must be after start date';
$string['error_backupfailed'] = 'Failed to create course backup';
$string['error_restorefailed'] = 'Failed to restore course backup';
$string['error_nopermission'] = 'You do not have permission to clone courses';
$string['error_invalidparameter'] = 'Invalid parameter: {$a}';

// Success messages
$string['success_coursecloned'] = 'Course cloned successfully';
$string['cloning_inprogress'] = 'Course cloning in progress...';
$string['backup_completed'] = 'Course backup completed';
$string['restore_completed'] = 'Course restore completed';