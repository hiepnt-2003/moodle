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
        global $DB;
        
        // Get event data.
        $batchid = $event->objectid;
        $startdate = $event->other['start_date'];
        
        // Log the event processing.
        mtrace("Event Observer: Processing batch_created event for batch ID: {$batchid}");
        
        try {
            // Use the batch manager to automatically add courses.
            $added_count = batch_manager::auto_add_courses_by_event($batchid, $startdate);
            
            // Log success.
            mtrace("Event Observer: Successfully added {$added_count} courses to batch {$batchid}");
            
            // Optionally, create a log entry in the database for tracking.
            self::log_event_processing('batch_created', $batchid, [
                'courses_added' => $added_count,
                'start_date' => $startdate,
                'status' => 'success'
            ]);
            
        } catch (\Exception $e) {
            // Log error.
            mtrace("Event Observer: Error processing batch_created event: " . $e->getMessage());
            
            // Log the error for debugging.
            self::log_event_processing('batch_created', $batchid, [
                'error' => $e->getMessage(),
                'start_date' => $startdate,
                'status' => 'error'
            ]);
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
        
        mtrace("Event Observer: Processing batch_updated event for batch ID: {$batchid}");
        
        try {
            // Get the batch to check if start date changed.
            $batch = batch_manager::get_batch($batchid);
            if (!$batch) {
                throw new \Exception("Batch not found: {$batchid}");
            }
            
            // Remove all event-added courses first.
            $DB->delete_records('local_testeventapi_courses', [
                'batchid' => $batchid,
                'added_by_event' => 1
            ]);
            
            // Re-add courses with new start date.
            $added_count = batch_manager::auto_add_courses_by_event($batchid, $newstartdate);
            
            mtrace("Event Observer: Refreshed courses for updated batch {$batchid}, added {$added_count} courses");
            
            self::log_event_processing('batch_updated', $batchid, [
                'courses_added' => $added_count,
                'new_start_date' => $newstartdate,
                'status' => 'success'
            ]);
            
        } catch (\Exception $e) {
            mtrace("Event Observer: Error processing batch_updated event: " . $e->getMessage());
            
            self::log_event_processing('batch_updated', $batchid, [
                'error' => $e->getMessage(),
                'new_start_date' => $newstartdate,
                'status' => 'error'
            ]);
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
        $courseid = $event->objectid;
        $batchid = $event->other['batchid'];
        $coursename = $event->other['coursename'];
        $addedbyevent = $event->other['added_by_event'];
        
        $method = $addedbyevent ? 'Event API' : 'Manual';
        
        mtrace("Event Observer: Course '{$coursename}' (ID: {$courseid}) added to batch {$batchid} via {$method}");
        
        // Log this event.
        self::log_event_processing('course_added_to_batch', $courseid, [
            'batchid' => $batchid,
            'coursename' => $coursename,
            'added_by_event' => $addedbyevent,
            'status' => 'success'
        ]);
    }

    /**
     * Log event processing for debugging and audit purposes.
     *
     * @param string $eventtype Type of event processed
     * @param int $objectid The object ID involved
     * @param array $data Additional data to log
     */
    private static function log_event_processing($eventtype, $objectid, $data) {
        global $DB;
        
        try {
            $log = new \stdClass();
            $log->eventtype = $eventtype;
            $log->objectid = $objectid;
            $log->data = json_encode($data);
            $log->timecreated = time();
            
            // Note: This would require creating a log table in install.xml
            // For now, we'll just use mtrace for logging.
            mtrace("Event Log: {$eventtype} - Object: {$objectid} - Data: " . json_encode($data));
            
        } catch (\Exception $e) {
            mtrace("Event Observer: Failed to log event processing: " . $e->getMessage());
        }
    }

    /**
     * Test function to demonstrate event API functionality.
     * This can be called manually to test the event system.
     */
    public static function test_event_api() {
        mtrace("Testing Event API functionality...");
        
        try {
            // Create a test batch which should trigger the event.
            $testname = 'Test Batch ' . date('Y-m-d H:i:s');
            $teststartdate = strtotime('+7 days'); // Next week.
            
            $batchid = batch_manager::create_batch($testname, $teststartdate);
            
            if ($batchid) {
                mtrace("Test Event API: Successfully created test batch with ID: {$batchid}");
                
                // Get statistics to see the results.
                $stats = batch_manager::get_batch_statistics($batchid);
                if ($stats) {
                    mtrace("Test Results:");
                    mtrace("- Total courses: {$stats->total_courses}");
                    mtrace("- Courses added by event: {$stats->courses_by_event}");
                    mtrace("- Courses added manually: {$stats->courses_manual}");
                    mtrace("- Available courses not added: {$stats->available_courses}");
                }
            } else {
                mtrace("Test Event API: Failed to create test batch");
            }
            
        } catch (\Exception $e) {
            mtrace("Test Event API: Error - " . $e->getMessage());
        }
    }
}