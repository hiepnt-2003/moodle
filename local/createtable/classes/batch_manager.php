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
 * Batch manager class for handling batch operations.
 *
 * @package    local_createtable
 * @copyright  2025 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_createtable;

/**
 * Class for managing batches and course assignments.
 *
 * This class provides methods for creating, updating, and deleting batches,
 * as well as managing automatic course assignments based on start dates.
 *
 * @package    local_createtable
 * @copyright  2025 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class batch_manager {
    
    /**
     * Get all batches ordered by open date.
     *
     * @return array Array of batch objects
     */
    public static function get_all_batches() {
        global $DB;
        
        return $DB->get_records('local_createtable_batches', null, 'open_date DESC');
    }
    
    /**
     * Get a single batch by ID.
     *
     * @param int $id Batch ID
     * @return object|false Batch object or false if not found
     */
    public static function get_batch($id) {
        global $DB;
        
        return $DB->get_record('local_createtable_batches', ['id' => $id]);
    }
    
    /**
     * Create a new batch.
     *
     * @param string $name Batch name
     * @param int $opendate Open date timestamp
     * @return int New batch ID
     * @throws \dml_exception
     */
    public static function create_batch($name, $opendate) {
        global $DB;
        
        $batch = new \stdClass();
        $batch->name = $name;
        $batch->open_date = $opendate;
        $batch->timecreated = time();
        
        $batchid = $DB->insert_record('local_createtable_batches', $batch);
        
        // Automatically add courses with matching start date.
        if ($batchid) {
            self::auto_add_courses_by_date($batchid, $opendate);
        }
        
        return $batchid;
    }
    
    /**
     * Update an existing batch.
     *
     * @param int $id Batch ID
     * @param string $name Batch name
     * @param int $opendate Open date timestamp
     * @return bool Success status
     * @throws \dml_exception
     */
    public static function update_batch($id, $name, $opendate) {
        global $DB;
        
        // Get current batch data to check if date changed.
        $currentbatch = $DB->get_record('local_createtable_batches', ['id' => $id]);
        
        $batch = new \stdClass();
        $batch->id = $id;
        $batch->name = $name;
        $batch->open_date = $opendate;
        
        $result = $DB->update_record('local_createtable_batches', $batch);
        
        // If date changed, refresh course assignments.
        if ($result && $currentbatch && $currentbatch->open_date != $opendate) {
            // Remove existing course assignments.
            $DB->delete_records('local_createtable_courses', ['batchid' => $id]);
            // Add courses with new matching date.
            self::auto_add_courses_by_date($id, $opendate);
        }
        
        return $result;
    }
    
    /**
     * Delete a batch.
     *
     * @param int $id Batch ID
     * @return bool Success status
     * @throws \dml_exception
     */
    public static function delete_batch($id) {
        global $DB;
        
        // Delete related courses first.
        $DB->delete_records('local_createtable_courses', ['batchid' => $id]);
        
        // Delete the batch.
        return $DB->delete_records('local_createtable_batches', ['id' => $id]);
    }
    
    /**
     * Get courses for a specific batch.
     *
     * @param int $batchid Batch ID
     * @return array Array of course objects
     * @throws \dml_exception
     */
    public static function get_batch_courses($batchid) {
        global $DB;
        
        $sql = "SELECT c.id, c.fullname, c.shortname, btc.timecreated as added_time
                FROM {local_createtable_courses} btc
                JOIN {course} c ON btc.courseid = c.id
                WHERE btc.batchid = ?
                ORDER BY c.fullname ASC";
        
        return $DB->get_records_sql($sql, [$batchid]);
    }
    
    /**
     * Add a course to a batch.
     *
     * @param int $batchid Batch ID
     * @param int $courseid Course ID
     * @return int|false New record ID or false if already exists
     * @throws \dml_exception
     */
    public static function add_course_to_batch($batchid, $courseid) {
        global $DB;
        
        // Check if already exists.
        if ($DB->record_exists('local_createtable_courses', ['batchid' => $batchid, 'courseid' => $courseid])) {
            return false;
        }
        
        $record = new \stdClass();
        $record->batchid = $batchid;
        $record->courseid = $courseid;
        $record->timecreated = time();
        
        return $DB->insert_record('local_createtable_courses', $record);
    }
    
    /**
     * Remove a course from a batch.
     *
     * @param int $batchid Batch ID
     * @param int $courseid Course ID
     * @return bool Success status
     * @throws \dml_exception
     */
    public static function remove_course_from_batch($batchid, $courseid) {
        global $DB;
        
        return $DB->delete_records('local_createtable_courses', ['batchid' => $batchid, 'courseid' => $courseid]);
    }
    
    /**
     * Automatically add courses with matching start date to batch.
     *
     * This method finds all visible courses that start on the same date as the
     * batch open date and automatically adds them to the batch.
     *
     * @param int $batchid Batch ID
     * @param int $opendate Open date timestamp
     * @return int Number of courses added
     * @throws \dml_exception
     */
    public static function auto_add_courses_by_date($batchid, $opendate) {
        global $DB;
        
        // Convert timestamp to start and end of day.
        $startofday = strtotime('today', $opendate);
        $endofday = strtotime('tomorrow', $opendate) - 1;
        
        // Find courses with start date matching the batch open date.
        $sql = "SELECT c.id, c.fullname, c.shortname, c.startdate
                FROM {course} c
                WHERE c.id != 1 
                  AND c.startdate >= ? 
                  AND c.startdate <= ?
                  AND c.visible = 1
                  AND NOT EXISTS (
                      SELECT 1 FROM {local_createtable_courses} btc 
                      WHERE btc.courseid = c.id AND btc.batchid = ?
                  )
                ORDER BY c.fullname ASC";
        
        $courses = $DB->get_records_sql($sql, [$startofday, $endofday, $batchid]);
        
        $added = 0;
        foreach ($courses as $course) {
            if (self::add_course_to_batch($batchid, $course->id)) {
                $added++;
            }
        }
        
        return $added;
    }
    
    /**
     * Get courses that match a specific date.
     *
     * This method is used for preview purposes before creating a batch
     * to show which courses will be automatically added.
     *
     * @param int $opendate Open date timestamp
     * @return array Array of matching courses
     * @throws \dml_exception
     */
    public static function get_courses_by_date($opendate) {
        global $DB;
        
        // Convert timestamp to start and end of day.
        $startofday = strtotime('today', $opendate);
        $endofday = strtotime('tomorrow', $opendate) - 1;
        
        $sql = "SELECT c.id, c.fullname, c.shortname, c.startdate, c.category
                FROM {course} c
                LEFT JOIN {course_categories} cc ON c.category = cc.id
                WHERE c.id != 1 
                  AND c.startdate >= ? 
                  AND c.startdate <= ?
                  AND c.visible = 1
                ORDER BY c.fullname ASC";
        
        $courses = $DB->get_records_sql($sql, [$startofday, $endofday]);
        
        // Add formatted date for display.
        foreach ($courses as $course) {
            $course->startdate_formatted = date('d/m/Y H:i', $course->startdate);
        }
        
        return $courses;
    }
    
    /**
     * Get batch statistics including auto-added courses info.
     *
     * This method provides statistics about courses in a batch, including
     * how many were automatically added vs manually added.
     *
     * @param int $batchid Batch ID
     * @return object|false Statistics object or false if batch not found
     * @throws \dml_exception
     */
    public static function get_batch_statistics($batchid) {
        global $DB;
        
        $batch = self::get_batch($batchid);
        if (!$batch) {
            return false;
        }
        
        $stats = new \stdClass();
        $stats->batch = $batch;
        
        // Count total courses in batch.
        $stats->total_courses = $DB->count_records('local_createtable_courses', ['batchid' => $batchid]);
        
        // Count courses that match the batch date.
        $startofday = strtotime('today', $batch->open_date);
        $endofday = strtotime('tomorrow', $batch->open_date) - 1;
        
        $sql = "SELECT COUNT(*) 
                FROM {local_createtable_courses} btc
                JOIN {course} c ON btc.courseid = c.id
                WHERE btc.batchid = ?
                  AND c.startdate >= ?
                  AND c.startdate <= ?";
        
        $stats->matching_date_courses = $DB->count_records_sql($sql, [$batchid, $startofday, $endofday]);
        
        // Count courses available on this date but not in batch.
        $sql = "SELECT COUNT(*)
                FROM {course} c
                WHERE c.id != 1 
                  AND c.startdate >= ? 
                  AND c.startdate <= ?
                  AND c.visible = 1
                  AND NOT EXISTS (
                      SELECT 1 FROM {local_createtable_courses} btc 
                      WHERE btc.courseid = c.id AND btc.batchid = ?
                  )";
        
        $stats->available_courses = $DB->count_records_sql($sql, [$startofday, $endofday, $batchid]);
        
        return $stats;
    }
}