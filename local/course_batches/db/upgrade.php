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
 * Upgrade script for local_course_batches plugin
 *
 * @package    local_course_batches
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade the local_course_batches plugin
 * @param int $oldversion the version we are upgrading from
 */
function xmldb_local_course_batches_upgrade($oldversion) {
    global $DB;
    
    $dbman = $DB->get_manager();

    if ($oldversion < 2025092501) {
        // Define table local_course_batch_courses to be created.
        $table = new xmldb_table('local_course_batch_courses');

        // Adding fields to table local_course_batch_courses.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('batchid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table local_course_batch_courses.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('batchid', XMLDB_KEY_FOREIGN, ['batchid'], 'local_course_batches', ['id']);
        $table->add_key('courseid', XMLDB_KEY_FOREIGN, ['courseid'], 'course', ['id']);

        // Adding indexes to table local_course_batch_courses.
        $table->add_index('batchid_idx', XMLDB_INDEX_NOTUNIQUE, ['batchid']);
        $table->add_index('courseid_idx', XMLDB_INDEX_NOTUNIQUE, ['courseid']);
        $table->add_index('batch_course', XMLDB_INDEX_UNIQUE, ['batchid', 'courseid']);

        // Conditionally launch create table for local_course_batch_courses.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Add end_date field to local_course_batches table.
        $table = new xmldb_table('local_course_batches');
        $field = new xmldb_field('end_date', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'start_date');

        // Conditionally launch add field end_date.
        if (!$dbman->field_exists($table, $field)) {
            // Set default end_date = start_date + 30 days for existing records
            $dbman->add_field($table, $field);
            $DB->execute("UPDATE {local_course_batches} SET end_date = start_date + (30 * 24 * 60 * 60) WHERE end_date = 0 OR end_date IS NULL");
        }

        // Add description field to local_course_batches table.
        $field = new xmldb_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null, 'created_date');

        // Conditionally launch add field description.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Remove course_count field from local_course_batches table as it's now calculated dynamically.
        $field = new xmldb_field('course_count');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Course_batches savepoint reached.
        upgrade_plugin_savepoint(true, 2025092501, 'local', 'course_batches');
    }

    return true;
}