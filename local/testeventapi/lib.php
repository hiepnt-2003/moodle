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
 * Library functions for local_testeventapi plugin.
 *
 * @package    local_testeventapi
 * @copyright  2025 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Add navigation entries for the plugin.
 *
 * @param global_navigation $navigation
 */
function local_testeventapi_extend_navigation(global_navigation $navigation) {
    global $USER;
    
    // Only add navigation if user has permission to view.
    if (!has_capability('local/testeventapi:view', context_system::instance())) {
        return;
    }
    
    // Add to site administration.
    $node = $navigation->add(
        get_string('pluginname', 'local_testeventapi'),
        new moodle_url('/local/testeventapi/index.php'),
        navigation_node::TYPE_CUSTOM,
        null,
        'local_testeventapi',
        new pix_icon('i/settings', '')
    );
    
    $node->showinflatnavigation = true;
}

/**
 * Add entries to the settings navigation.
 *
 * @param settings_navigation $navigation
 * @param context $context
 */
function local_testeventapi_extend_settings_navigation(settings_navigation $navigation, context $context) {
    global $PAGE;
    
    // Only add to site administration context.
    if ($context->contextlevel !== CONTEXT_SYSTEM) {
        return;
    }
    
    // Only show if user has permission.
    if (!has_capability('local/testeventapi:view', context_system::instance())) {
        return;
    }
    
    // Find or create the local plugins node.
    $localplugins = $navigation->get('localplugins');
    if (!$localplugins) {
        $localplugins = $navigation->add(
            get_string('localplugins', 'admin'),
            null,
            navigation_node::TYPE_CATEGORY,
            null,
            'localplugins'
        );
    }
    
    // Add the Test Event API management link.
    $testeventapi = $localplugins->add(
        get_string('pluginname', 'local_testeventapi'),
        null,
        navigation_node::TYPE_CATEGORY,
        null,
        'local_testeventapi'
    );
    
    $testeventapi->add(
        get_string('managebatches', 'local_testeventapi'),
        new moodle_url('/local/testeventapi/index.php'),
        navigation_node::TYPE_SETTING,
        null,
        'testeventapi_manage'
    );
    
    if (has_capability('local/testeventapi:manage', context_system::instance())) {
        $testeventapi->add(
            get_string('testevent', 'local_testeventapi'),
            new moodle_url('/local/testeventapi/test_api.php'),
            navigation_node::TYPE_SETTING,
            null,
            'testeventapi_test'
        );
    }
}

/**
 * Get courses by date (used by AJAX).
 *
 * @param int $startdate Start date timestamp
 * @return array List of matching courses
 */
function local_testeventapi_get_courses_by_date($startdate) {
    return \local_testeventapi\batch_manager::get_courses_by_date($startdate);
}

/**
 * Fragment callback for course preview.
 *
 * @param array $args
 * @return string HTML content
 */
function local_testeventapi_output_fragment_course_preview($args) {
    global $OUTPUT;
    
    $startdate = $args['startdate'] ?? 0;
    
    if (!$startdate) {
        return '<div class="alert alert-info">Chọn ngày để xem preview.</div>';
    }
    
    $courses = \local_testeventapi\batch_manager::get_courses_by_date($startdate);
    
    if (empty($courses)) {
        return '<div class="alert alert-warning">Không có môn học nào bắt đầu vào ngày này.</div>';
    }
    
    $html = '<div class="alert alert-success">';
    $html .= '<strong>Tìm thấy ' . count($courses) . ' môn học:</strong>';
    $html .= '<ul class="mt-2">';
    
    foreach ($courses as $course) {
        $html .= '<li>' . format_string($course->fullname) . ' (' . format_string($course->shortname) . ')';
        if (isset($course->startdate_formatted)) {
            $html .= '<br><small class="text-muted">Bắt đầu: ' . $course->startdate_formatted . '</small>';
        }
        $html .= '</li>';
    }
    
    $html .= '</ul>';
    $html .= '<small class="text-muted">Các môn học này sẽ được tự động thêm vào đợt.</small>';
    $html .= '</div>';
    
    return $html;
}

/**
 * Perform cleanup when plugin is uninstalled.
 */
function local_testeventapi_uninstall() {
    global $DB;
    
    // Clean up any configuration.
    unset_all_config_for_plugin('local_testeventapi');
    
    return true;
}

/**
 * Check if the plugin tables exist and are set up correctly.
 *
 * @return bool True if tables exist, false otherwise
 */
function local_testeventapi_check_database() {
    global $DB;
    
    $dbman = $DB->get_manager();
    
    // Check if both tables exist.
    $batchtable = new xmldb_table('local_testeventapi_batches');
    $coursetable = new xmldb_table('local_testeventapi_courses');
    
    return $dbman->table_exists($batchtable) && $dbman->table_exists($coursetable);
}

/**
 * Get plugin statistics.
 *
 * @return object Statistics about the plugin usage
 */
function local_testeventapi_get_statistics() {
    global $DB;
    
    $stats = new stdClass();
    $stats->total_batches = $DB->count_records('local_testeventapi_batches');
    $stats->total_courses = $DB->count_records('local_testeventapi_courses');
    $stats->event_courses = $DB->count_records('local_testeventapi_courses', ['added_by_event' => 1]);
    $stats->manual_courses = $DB->count_records('local_testeventapi_courses', ['added_by_event' => 0]);
    
    // Calculate percentages.
    if ($stats->total_courses > 0) {
        $stats->event_percentage = round(($stats->event_courses / $stats->total_courses) * 100, 1);
        $stats->manual_percentage = round(($stats->manual_courses / $stats->total_courses) * 100, 1);
    } else {
        $stats->event_percentage = 0;
        $stats->manual_percentage = 0;
    }
    
    return $stats;
}