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
 * Library functions for report_userreport.
 *
 * @package    report_userreport
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Extends the settings navigation with the report items.
 *
 * @param settings_navigation $settingsnav The settings navigation object
 * @param context $context The context of the page
 */
function report_userreport_extend_settings_navigation(settings_navigation $settingsnav, context $context) {
    if ($context->contextlevel == CONTEXT_SYSTEM && has_capability('report/userreport:view', $context)) {
        if ($reportnode = $settingsnav->find('reports', navigation_node::TYPE_CONTAINER)) {
            $url = new moodle_url('/report/userreport/index.php');
            $reportnode->add(
                get_string('pluginname', 'report_userreport'),
                $url,
                navigation_node::TYPE_SETTING,
                null,
                'report_userreport',
                new pix_icon('i/report', '')
            );
        }
    }
}