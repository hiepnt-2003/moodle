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
 * Monthly course creation scheduled task for local_createtable.
 *
 * @package    local_createtable
 * @copyright  2025 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_createtable\task;

/**
 * Scheduled task to create a new batch with courses on the 1st of each month at 5:00 AM.
 *
 * This task automatically creates a new batch and adds matching courses
 * based on the configured date pattern.
 *
 * @package    local_createtable
 * @copyright  2025 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class monthly_course_creation extends \core\task\scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('monthlycoursecreationnametask', 'local_createtable');
    }

    /**
     * Execute the scheduled task.
     */
    public function execute() {
        global $DB;

        mtrace('Starting monthly course creation task...');

        try {
            // Check if auto-assign is enabled in settings.
            $autoassign = get_config('local_createtable', 'autoassign');
            if (!$autoassign) {
                mtrace('Auto-assign is disabled in settings. Task aborted.');
                return;
            }

            // Get the current date (first day of current month).
            $current_time = time();
            $batch_date = strtotime('first day of this month', $current_time);
            
            // Create batch name based on current month/year.
            $batch_name = get_string('monthlybatchname', 'local_createtable', 
                date('m/Y', $current_time));

            // Check if batch for this month already exists.
            $existing_batch = $DB->get_record('local_createtable_batches', [
                'name' => $batch_name
            ]);

            if ($existing_batch) {
                mtrace('Batch for this month already exists: ' . $batch_name);
                return;
            }

            // Create new batch.
            $batch_data = new \stdClass();
            $batch_data->name = $batch_name;
            $batch_data->open_date = $batch_date;
            $batch_data->timecreated = $current_time;
            $batch_data->timemodified = $current_time;

            $batch_id = $DB->insert_record('local_createtable_batches', $batch_data);
            mtrace('Created new batch: ' . $batch_name . ' (ID: ' . $batch_id . ')');

            // Auto-add courses with matching start date.
            $manager = new \local_createtable\batch_manager();
            $added_count = $manager->auto_add_courses_by_date($batch_id, $batch_date);

            mtrace('Added ' . $added_count . ' courses to batch ' . $batch_name);

            // Log the activity.
            $this->log_batch_creation($batch_id, $batch_name, $added_count);

            mtrace('Monthly course creation task completed successfully.');

        } catch (\Exception $e) {
            mtrace('Error in monthly course creation task: ' . $e->getMessage());
            throw $e; // Re-throw to mark task as failed.
        }
    }

    /**
     * Log the batch creation activity.
     *
     * @param int $batch_id The created batch ID
     * @param string $batch_name The batch name
     * @param int $course_count Number of courses added
     */
    private function log_batch_creation($batch_id, $batch_name, $course_count) {
        // Create a system context event for logging.
        $context = \context_system::instance();
        
        $event = \core\event\base::create([
            'context' => $context,
            'other' => [
                'batch_id' => $batch_id,
                'batch_name' => $batch_name,
                'course_count' => $course_count,
                'created_by' => 'scheduled_task'
            ]
        ]);

        // For now, just use mtrace for logging.
        // In a full implementation, you might want to create a custom event class.
        mtrace('Batch creation logged: ' . $batch_name . ' with ' . $course_count . ' courses');
    }
}