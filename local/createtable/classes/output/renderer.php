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
 * Output renderer helper for local_createtable.
 *
 * @package    local_createtable
 * @copyright  2025 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_createtable\output;

/**
 * Output renderer helper class.
 *
 * This class provides helper methods for preparing data for Mustache templates
 * and including necessary CSS/JavaScript assets.
 *
 * @package    local_createtable
 * @copyright  2025 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer {
    
    /**
     * Get data for batch list template.
     *
     * @return array Template data
     */
    public static function get_batch_list_data() {
        global $DB;
        
        // Include CSS assets.
        self::include_assets();
        
        $batches = $DB->get_records('local_createtable_batches', null, 'open_date DESC');
        
        $templatedata = [
            'batches' => [],
            'hasbatches' => !empty($batches),
            'nobatches_message' => get_string('nobatches', 'local_createtable'),
            'addbatch_url' => (new \moodle_url('/local/createtable/manage.php'))->out(false),
            'addbatch_text' => get_string('addbatch', 'local_createtable'),
        ];
        
        foreach ($batches as $batch) {
            $templatedata['batches'][] = [
                'id' => $batch->id,
                'name' => format_string($batch->name),
                'opendate' => self::format_datetime($batch->open_date),
                'timecreated' => self::format_datetime($batch->timecreated),
                'edit_url' => (new \moodle_url('/local/createtable/manage.php', ['id' => $batch->id]))->out(false),
                'view_url' => (new \moodle_url('/local/createtable/view.php', ['id' => $batch->id]))->out(false),
                'edit_text' => get_string('edit'),
                'view_text' => get_string('view'),
            ];
        }
        
        return $templatedata;
    }
    
    /**
     * Get data for batch detail template
     * 
     * @param object $batch Batch object
     * @return array Template data
     */
    public static function get_batch_detail_data($batch) {
        global $DB;
        
        // Include CSS assets
        self::include_assets();
        
        // Get batch statistics
        $stats = \local_createtable\batch_manager::get_batch_statistics($batch->id);
        
        // Get courses for this batch with start date info
        $sql = "SELECT c.id, c.fullname, c.shortname, c.startdate, btc.timecreated as added_time
                FROM {local_createtable_courses} btc
                JOIN {course} c ON btc.courseid = c.id
                WHERE btc.batchid = ?
                ORDER BY c.fullname ASC";
        $courses = $DB->get_records_sql($sql, [$batch->id]);
        
        $templatedata = [
            'batch' => [
                'id' => $batch->id,
                'name' => format_string($batch->name),
                'opendate' => self::format_datetime($batch->open_date),
                'opendate_only' => self::format_date($batch->open_date),
                'timecreated' => self::format_datetime($batch->timecreated)
            ],
            'courses' => [],
            'hascourses' => !empty($courses),
            'nocourses_message' => get_string('nocourses', 'local_createtable'),
            'back_url' => (new moodle_url('/local/createtable/index.php'))->out(false),
            'back_text' => get_string('back', 'local_createtable'),
            'edit_url' => (new moodle_url('/local/createtable/manage.php', ['id' => $batch->id]))->out(false),
            'edit_text' => get_string('edit'),
            'refresh_url' => (new moodle_url('/local/createtable/refresh_courses.php', ['id' => $batch->id]))->out(false),
            'refresh_text' => get_string('refresh_courses', 'local_createtable'),
            'statistics' => [
                'total_courses' => $stats->total_courses,
                'matching_courses' => $stats->matching_date_courses,
                'available_courses' => $stats->available_courses
            ]
        ];
        
        // Batch open date for comparison
        $batch_date = self::format_date($batch->open_date);
        
        foreach ($courses as $course) {
            $course_date = self::format_date($course->startdate);
            $is_matching_date = ($course_date === $batch_date);
            
            $templatedata['courses'][] = [
                'id' => $course->id,
                'fullname' => format_string($course->fullname),
                'shortname' => format_string($course->shortname),
                'startdate' => self::format_datetime($course->startdate),
                'added_time' => self::format_datetime($course->added_time),
                'course_url' => (new moodle_url('/course/view.php', ['id' => $course->id]))->out(false),
                'is_matching_date' => $is_matching_date,
                'date_match_class' => $is_matching_date ? 'matching-date' : 'different-date'
            ];
        }
        
        return $templatedata;
    }
    
    /**
     * Get data for batch form template
     * 
     * @param object|null $batch Batch object for editing, null for creating
     * @return array Template data
     */
    public static function get_batch_form_data($batch = null) {
        global $PAGE;
        
        // Include CSS and JS
        self::include_assets();
        
        $isediting = !empty($batch);
        
        $templatedata = [
            'isediting' => $isediting,
            'form_title' => $isediting ? get_string('editbatch', 'local_createtable') : get_string('addbatch', 'local_createtable'),
            'form_action' => (new moodle_url('/local/createtable/manage.php'))->out(false),
            'sesskey' => sesskey(),
            'batch' => null,
            'cancel_url' => (new moodle_url('/local/createtable/index.php'))->out(false),
            'cancel_text' => get_string('cancel'),
            'save_text' => get_string('save', 'local_createtable')
        ];
        
        if ($isediting) {
            $templatedata['batch'] = [
                'id' => $batch->id,
                'name' => format_string($batch->name),
                'opendate' => date('Y-m-d', $batch->open_date)
            ];
        }
        
        // Initialize JavaScript for form validation
        $PAGE->requires->js_call_amd('local_createtable/batch_form', 'init');
        
        return $templatedata;
    }
    
    /**
     * Include CSS and JS assets
     */
    public static function include_assets() {
        global $PAGE, $CFG;
        
        // Include CSS
        $PAGE->requires->css('/local/createtable/styles/styles.css');
        
        // Include any additional stylesheets if needed
        if (file_exists($CFG->dirroot . '/local/createtable/styles/custom.css')) {
            $PAGE->requires->css('/local/createtable/styles/custom.css');
        }
    }
    
    /**
     * Format datetime for display
     * 
     * @param int $timestamp Unix timestamp
     * @return string Formatted datetime
     */
    private static function format_datetime($timestamp) {
        if (empty($timestamp)) {
            return '';
        }
        return userdate($timestamp, get_string('strftimedatetime', 'langconfig'));
    }
    
    /**
     * Format date only for comparison
     * 
     * @param int $timestamp Unix timestamp
     * @return string Formatted date (Y-m-d)
     */
    private static function format_date($timestamp) {
        if (empty($timestamp)) {
            return '';
        }
        return date('Y-m-d', $timestamp);
    }
}