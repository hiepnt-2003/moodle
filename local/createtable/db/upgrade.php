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
 * Upgrade script for local_createtable plugin.
 *
 * @package    local_createtable
 * @copyright  2025 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Function to upgrade local_createtable plugin.
 *
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_local_createtable_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2025092603) {
        // Add any future upgrade steps here.
        
        // Createtable savepoint reached.
        upgrade_plugin_savepoint(true, 2025092603, 'local', 'createtable');
    }

    if ($oldversion < 2025092604) {
        // Install scheduled task for monthly course creation.
        // The task definition is in db/tasks.php and will be automatically installed.
        
        // Createtable savepoint reached.
        upgrade_plugin_savepoint(true, 2025092604, 'local', 'createtable');
    }

    return true;
}