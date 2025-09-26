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
 * Event observer for local_testeventapi.
 *
 * @package    local_testeventapi
 * @copyright  2025 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_testeventapi;

/**
 * Event observer class.
 *
 * This class observes various events and performs automated actions
 * such as adding courses to batches when events are triggered.
 *
 * @package    local_testeventapi
 * @copyright  2025 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class observer {

    /**
     * Observer function to handle batch created event.
     * 
     * When a new batch is created, this method automatically scans all courses
     * in Moodle and adds courses that have the same start date as the batch.
     *
     * @param \local_testeventapi\event\batch_created $event The batch created event
     */
    public static function batch_created(\local_testeventapi\event\batch_created $event) {
        // Get event data.
        $batchid = $event->objectid;
        $startdate = $event->other['start_date'];
        
        try {
            // Use the batch manager to automatically add courses.
            batch_manager::auto_add_courses_by_event($batchid, $startdate);
        } catch (\Exception $e) {
            // Silently handle errors to avoid disrupting the system.
        }
    }

    /**
     * Observer function to handle batch updated event.
     * 
     * When a batch is updated (especially if start date changes), 
     * this method re-scans and updates course assignments.
     *
     * @param \local_testeventapi\event\batch_updated $event The batch updated event
     */
    public static function batch_updated(\local_testeventapi\event\batch_updated $event) {
        global $DB;
        
        $batchid = $event->objectid;
        $newstartdate = $event->other['start_date'];
        
        try {
            // Get the batch to check if start date changed.
            $batch = batch_manager::get_batch($batchid);
            if (!$batch) {
                return;
            }
            
            // Remove all event-added courses first.
            $DB->delete_records('local_testeventapi_courses', [
                'batchid' => $batchid,
                'added_by_event' => 1
            ]);
            
            // Re-add courses with new start date.
            batch_manager::auto_add_courses_by_event($batchid, $newstartdate);
            
        } catch (\Exception $e) {
            // Silently handle errors.
        }
    }

    /**
     * Observer function to handle batch deleted event.
     * 
     * When a batch is deleted, this method sends email notification to admin.
     *
     * @param \local_testeventapi\event\batch_deleted $event The batch deleted event
     */
    public static function batch_deleted(\local_testeventapi\event\batch_deleted $event) {
        global $USER;
        
        $batchid = $event->objectid;
        $batchname = $event->other['name'];
        $startdate = $event->other['start_date'];
        
        try {
            // Create a mock batch object for email.
            $batch = new \stdClass();
            $batch->id = $batchid;
            $batch->name = $batchname;
            $batch->start_date = $startdate;
            $batch->timecreated = time();
            
            // Create mock stats.
            $stats = new \stdClass();
            $stats->total_courses = 0;
            $stats->courses_by_event = 0;
            $stats->courses_manual = 0;
            
            // Send email notification.
            batch_manager::send_batch_deletion_email($batch, $stats, $USER);
            
        } catch (\Exception $e) {
            // Silently handle errors.
        }
    }

    /**
     * Observer function to handle course added to batch event.
     * 
     * This can be used for additional processing when courses are added.
     *
     * @param \local_testeventapi\event\course_added_to_batch $event The course added event
     */
    public static function course_added_to_batch(\local_testeventapi\event\course_added_to_batch $event) {
        // This observer is available for future extensions.
        // Currently no additional processing is needed.
    }
}