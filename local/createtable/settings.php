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
 * Settings for local_createtable plugin.
 *
 * @package    local_createtable
 * @copyright  2025 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_createtable', get_string('pluginname', 'local_createtable'));

    // Auto-assignment settings.
    $settings->add(new admin_setting_configcheckbox(
        'local_createtable/autoassign_enabled',
        get_string('autoassign_enabled', 'local_createtable'),
        get_string('autoassign_enabled_desc', 'local_createtable'),
        1
    ));

    $settings->add(new admin_setting_configtext(
        'local_createtable/default_batch_prefix',
        get_string('default_batch_prefix', 'local_createtable'),
        get_string('default_batch_prefix_desc', 'local_createtable'),
        'Batch',
        PARAM_TEXT
    ));

    // Add a link to the main management page.
    $settings->add(new admin_setting_heading(
        'local_createtable/managementlink',
        get_string('management', 'local_createtable'),
        html_writer::link(
            new moodle_url('/local/createtable/index.php'),
            get_string('managebatches', 'local_createtable')
        )
    ));

    $ADMIN->add('localplugins', $settings);
}