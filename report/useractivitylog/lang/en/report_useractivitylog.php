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
 * Language strings for User Activity Log report plugin.
 *
 * @package    report_useractivitylog
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'User Activity Log';
$string['privacy:metadata'] = 'The User Activity Log plugin shows existing log data but does not store any personal data itself.';
$string['useractivitylog:view'] = 'View user activity log report';

// Filter form strings
$string['selectuser'] = 'Select user';
$string['selectuser_help'] = 'Choose a user to view their activity log';
$string['startdate'] = 'Start date';
$string['enddate'] = 'End date';
$string['selectcourse'] = 'Select course';
$string['selectcourse_help'] = 'Choose a course to filter activities (leave empty for all courses)';
$string['allcourses'] = 'All courses';
$string['filterbutton'] = 'Filter';
$string['resetfilter'] = 'Reset';

// Table headers
$string['time'] = 'Time';
$string['fullname'] = 'User full name';
$string['affecteduser'] = 'Affected user';
$string['eventcontext'] = 'Event context';
$string['component'] = 'Component';
$string['eventname'] = 'Event name';
$string['description'] = 'Description';
$string['origin'] = 'Origin';
$string['ipaddress'] = 'IP address';

// Messages
$string['nodata'] = 'No activity data found for the selected criteria.';
$string['invaliduser'] = 'Invalid user selected.';
$string['daterangeerror'] = 'End date must be after start date.';
$string['selectuserhelp'] = 'Please select a user to view their activity log.';

// Page title
$string['reporttitle'] = 'User Activity Log Report';