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
 * Language strings for the Course Clone plugin.
 *
 * @package    local_courseclone
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Course Clone';
$string['courseclone:clone'] = 'Clone courses';

// Error messages
$string['error_coursenotfound'] = 'Course not found with shortname: {$a}';
$string['error_shortnameinuse'] = 'Shortname already in use: {$a}';
$string['error_invaliddate'] = 'Invalid date format';
$string['error_enddatebeforestartdate'] = 'End date must be after start date';
$string['error_invalidparameter'] = 'Invalid parameter: {$a}';
$string['error_backupfailed'] = 'Backup failed';
$string['error_restorefailed'] = 'Restore failed';

// Success messages
$string['success_coursecloned'] = 'Course cloned successfully!';
$string['backup_completed'] = 'Backup completed';
$string['restore_completed'] = 'Restore completed';