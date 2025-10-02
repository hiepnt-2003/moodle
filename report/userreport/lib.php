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
 * Library functions for User Activity Report plugin.
 *
 * @package    report_userreport
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Add this report to the list of reports in the system.
 *
 * @param array $reports
 * @param array $courseid
 * @return array
 */
function report_userreport_extend_navigation_system(navigation_node $navigation) {
    if (has_capability('report/userreport:view', context_system::instance())) {
        $url = new moodle_url('/report/userreport/index.php');
        $navigation->add(
            get_string('pluginname', 'report_userreport'),
            $url,
            navigation_node::TYPE_SETTING,
            null,
            'userreport',
            new pix_icon('i/report', '')
        );
    }
}

/**
 * This function extends the global navigation tree by adding report nodes if there is relevant content.
 *
 * @param global_navigation $navigation
 */
function report_userreport_extend_navigation(global_navigation $navigation) {
    if (has_capability('report/userreport:view', context_system::instance())) {
        $reportnode = $navigation->find('reports', navigation_node::TYPE_CONTAINER);
        if ($reportnode) {
            $url = new moodle_url('/report/userreport/index.php');
            $reportnode->add(
                get_string('pluginname', 'report_userreport'),
                $url,
                navigation_node::TYPE_SETTING,
                null,
                'userreport',
                new pix_icon('i/report', '')
            );
        }
    }
}

/**
 * Add report link to admin reports page.
 *
 * @param admin_root $ADMIN
 */
function report_userreport_extend_settings_navigation($settingsnav, $context) {
    global $PAGE;

    if (has_capability('report/userreport:view', context_system::instance())) {
        if ($reportnode = $settingsnav->find('reports', navigation_node::TYPE_CONTAINER)) {
            $url = new moodle_url('/report/userreport/index.php');
            $reportnode->add(
                get_string('pluginname', 'report_userreport'),
                $url,
                navigation_node::TYPE_SETTING,
                null,
                'userreport'
            );
        }
    }
}