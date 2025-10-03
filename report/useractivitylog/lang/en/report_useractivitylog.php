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

$string['pluginname'] = 'Logs';
$string['privacy:metadata'] = 'The Logs report shows existing log data but does not store any personal data itself.';
$string['useractivitylog:view'] = 'View logs report';

// Filter form strings - compatible with standard logs
$string['logsheading'] = 'Logs';
$string['gettheselogs'] = 'Get these logs';
$string['allusers'] = 'All participants';
$string['allcourses'] = 'All courses';
$string['allactivities'] = 'All activities';
$string['allactions'] = 'All actions';
$string['alleducationlevels'] = 'All education levels';
$string['participants'] = 'Participants';
$string['activities'] = 'Activities';
$string['actions'] = 'Actions';
$string['edulevel'] = 'Education level';
$string['origin'] = 'Origin';
$string['logsperpage'] = 'Logs per page';
$string['educationlevelother'] = 'Other';
$string['educationlevelparticipating'] = 'Participating';
$string['educationlevelteaching'] = 'Teaching';
$string['invalidlogsperpage'] = 'Logs per page must be between 1 and 5000';

// Table headers
$string['time'] = 'Time';
$string['fullname'] = 'Full name';
$string['affecteduser'] = 'Affected user';
$string['eventcontext'] = 'Event context';
$string['component'] = 'Component';
$string['eventname'] = 'Event name';
$string['description'] = 'Description';
$string['ipaddress'] = 'IP address';

// Messages
$string['nodata'] = 'No logs found for the selected criteria.';
$string['nologs'] = 'No logs found.';
$string['logshowing'] = 'Displaying {$a->displayed} of {$a->total} entries';

// Standard Moodle strings used
$string['all'] = 'All';
$string['course'] = 'Course';
$string['date'] = 'Date';
$string['create'] = 'Create';
$string['view'] = 'View';
$string['update'] = 'Update';
$string['delete'] = 'Delete';

// Page title
$string['reporttitle'] = 'Logs';