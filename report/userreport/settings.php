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
 * Settings and links for User Activity Report plugin.
 *
 * @package    report_userreport
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

// Add the report to the admin menu.
if (has_capability('report/userreport:view', context_system::instance())) {
    $ADMIN->add('reports', new admin_externalpage(
        'reportuserreport',
        get_string('pluginname', 'report_userreport'),
        new moodle_url('/report/userreport/index.php'),
        'report/userreport:view'
    ));
}