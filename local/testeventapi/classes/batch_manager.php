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
 * Batch manager class for handling batch operations in Test Event API.
 *
 * @package    local_testeventapi
 * @copyright  2025 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_testeventapi;

/**
 * Class for managing batches and course assignments with event API integration.
 *
 * This class provides methods for creating, updating, and deleting batches,
 * as well as managing automatic course assignments triggered by events.
 *
 * @package    local_testeventapi
 * @copyright  2025 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class batch_manager {
    
    /**
     * Get all batches ordered by start date.
     *
     * @return array Array of batch objects
     */
    public static function get_all_batches() {
        global $DB;
        
        return $DB->get_records('local_testeventapi_batches', null, 'start_date DESC');
    }
    
    /**
     * Get a single batch by ID.
     *
     * @param int $id Batch ID
     * @return object|false Batch object or false if not found
     */
    public static function get_batch($id) {
        global $DB;
        
        return $DB->get_record('local_testeventapi_batches', ['id' => $id]);
    }
    
    /**
     * Create a new batch and trigger event.
     *
     * @param string $name Batch name
     * @param int $startdate Start date timestamp
     * @return int New batch ID
     * @throws \dml_exception
     */
    public static function create_batch($name, $startdate) {
        global $DB;
        
        $batch = new \stdClass();
        $batch->name = $name;
        $batch->start_date = $startdate;
        $batch->timecreated = time();
        $batch->timemodified = time();
        
        $batchid = $DB->insert_record('local_testeventapi_batches', $batch);
        
        if ($batchid) {
            // Trigger custom event for batch creation.
            $event = \local_testeventapi\event\batch_created::create([
                'context' => \context_system::instance(),
                'objectid' => $batchid,
                'other' => [
                    'name' => $name,
                    'start_date' => $startdate,
                ]
            ]);
            $event->trigger();
        }
        
        return $batchid;
    }
    
    /**
     * Update an existing batch.
     *
     * @param int $id Batch ID
     * @param string $name Batch name
     * @param int $startdate Start date timestamp
     * @return bool Success status
     * @throws \dml_exception
     */
    public static function update_batch($id, $name, $startdate) {
        global $DB;
        
        $batch = new \stdClass();
        $batch->id = $id;
        $batch->name = $name;
        $batch->start_date = $startdate;
        $batch->timemodified = time();
        
        $result = $DB->update_record('local_testeventapi_batches', $batch);
        
        if ($result) {
            // Trigger update event.
            $event = \local_testeventapi\event\batch_updated::create([
                'context' => \context_system::instance(),
                'objectid' => $id,
                'other' => [
                    'name' => $name,
                    'start_date' => $startdate,
                ]
            ]);
            $event->trigger();
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
        
        $batch = self::get_batch($id);
        if (!$batch) {
            return false;
        }
        
        // Delete related courses first.
        $DB->delete_records('local_testeventapi_courses', ['batchid' => $id]);
        
        // Delete the batch.
        $result = $DB->delete_records('local_testeventapi_batches', ['id' => $id]);
        
        if ($result) {
            // Trigger delete event.
            $event = \local_testeventapi\event\batch_deleted::create([
                'context' => \context_system::instance(),
                'objectid' => $id,
                'other' => [
                    'name' => $batch->name,
                    'start_date' => $batch->start_date,
                ]
            ]);
            $event->trigger();
        }
        
        return $result;
    }
    
    /**
     * Get courses for a specific batch.
     *
     * @param int $batchid Batch ID
     * @return array Array of course objects with additional info
     * @throws \dml_exception
     */
    public static function get_batch_courses($batchid) {
        global $DB;
        
        $sql = "SELECT c.id, c.fullname, c.shortname, c.startdate,
                       btc.timecreated as added_time, btc.added_by_event
                FROM {local_testeventapi_courses} btc
                JOIN {course} c ON btc.courseid = c.id
                WHERE btc.batchid = ?
                ORDER BY btc.added_by_event DESC, c.fullname ASC";
        
        return $DB->get_records_sql($sql, [$batchid]);
    }
    
    /**
     * Add a course to a batch.
     *
     * @param int $batchid Batch ID
     * @param int $courseid Course ID
     * @param bool $addedbyevent Whether added by event API
     * @return int|false New record ID or false if already exists
     * @throws \dml_exception
     */
    public static function add_course_to_batch($batchid, $courseid, $addedbyevent = false) {
        global $DB;
        
        // Check if already exists.
        if ($DB->record_exists('local_testeventapi_courses', ['batchid' => $batchid, 'courseid' => $courseid])) {
            return false;
        }
        
        $record = new \stdClass();
        $record->batchid = $batchid;
        $record->courseid = $courseid;
        $record->timecreated = time();
        $record->added_by_event = $addedbyevent ? 1 : 0;
        
        return $DB->insert_record('local_testeventapi_courses', $record);
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
        
        return $DB->delete_records('local_testeventapi_courses', ['batchid' => $batchid, 'courseid' => $courseid]);
    }
    
    /**
     * Automatically add courses with matching start date to batch (triggered by event).
     *
     * This method finds all visible courses that start on the same date as the
     * batch start date and automatically adds them to the batch via event API.
     *
     * @param int $batchid Batch ID
     * @param int $startdate Start date timestamp
     * @return int Number of courses added
     * @throws \dml_exception
     */
    public static function auto_add_courses_by_event($batchid, $startdate) {
        global $DB;
        
        // Convert timestamp to start and end of day.
        $startofday = strtotime('today', $startdate);
        $endofday = strtotime('tomorrow', $startdate) - 1;
        
        // Find courses with start date matching the batch start date.
        $sql = "SELECT c.id, c.fullname, c.shortname, c.startdate
                FROM {course} c
                WHERE c.id != 1 
                  AND c.startdate >= ? 
                  AND c.startdate <= ?
                  AND c.visible = 1
                  AND NOT EXISTS (
                      SELECT 1 FROM {local_testeventapi_courses} btc 
                      WHERE btc.courseid = c.id AND btc.batchid = ?
                  )
                ORDER BY c.fullname ASC";
        
        $courses = $DB->get_records_sql($sql, [$startofday, $endofday, $batchid]);
        
        $added = 0;
        foreach ($courses as $course) {
            if (self::add_course_to_batch($batchid, $course->id, true)) {
                $added++;
                
                // Trigger course added event.
                $event = \local_testeventapi\event\course_added_to_batch::create([
                    'context' => \context_system::instance(),
                    'objectid' => $course->id,
                    'other' => [
                        'batchid' => $batchid,
                        'coursename' => $course->fullname,
                        'added_by_event' => true,
                    ]
                ]);
                $event->trigger();
            }
        }
        
        return $added;
    }
    
    /**
     * Get courses that match a specific date (for preview).
     *
     * @param int $startdate Start date timestamp
     * @return array Array of matching courses
     * @throws \dml_exception
     */
    public static function get_courses_by_date($startdate) {
        global $DB;
        
        // Convert timestamp to start and end of day.
        $startofday = strtotime('today', $startdate);
        $endofday = strtotime('tomorrow', $startdate) - 1;
        
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
     * Get batch statistics including event-added courses info.
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
        $stats->total_courses = $DB->count_records('local_testeventapi_courses', ['batchid' => $batchid]);
        
        // Count courses added by event API.
        $stats->courses_by_event = $DB->count_records('local_testeventapi_courses', 
            ['batchid' => $batchid, 'added_by_event' => 1]);
        
        // Count courses added manually.
        $stats->courses_manual = $DB->count_records('local_testeventapi_courses', 
            ['batchid' => $batchid, 'added_by_event' => 0]);
        
        // Count courses available on this date but not in batch.
        $startofday = strtotime('today', $batch->start_date);
        $endofday = strtotime('tomorrow', $batch->start_date) - 1;
        
        $sql = "SELECT COUNT(*)
                FROM {course} c
                WHERE c.id != 1 
                  AND c.startdate >= ? 
                  AND c.startdate <= ?
                  AND c.visible = 1
                  AND NOT EXISTS (
                      SELECT 1 FROM {local_testeventapi_courses} btc 
                      WHERE btc.courseid = c.id AND btc.batchid = ?
                  )";
        
        $stats->available_courses = $DB->count_records_sql($sql, [$startofday, $endofday, $batchid]);
        
        return $stats;
    }
}