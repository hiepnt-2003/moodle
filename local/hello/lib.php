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
 * Library functions for local_hello plugin
 *
 * @package    local_hello
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Adds local_hello links to the navigation
 *
 * @param global_navigation $navigation
 */
function local_hello_extend_navigation(global_navigation $navigation) {
    global $PAGE;
    
    // Only add to site context
    if ($PAGE->context->contextlevel == CONTEXT_SYSTEM) {
        $node = $navigation->add(
            get_string('pluginname', 'local_hello'),
            new moodle_url('/local/hello/index.php'),
            navigation_node::TYPE_CUSTOM,
            null,
            'local_hello',
            new pix_icon('i/settings', '')
        );
        $node->showinflatnavigation = true;
    }
}

/**
 * Add navigation nodes to the settings navigation
 *
 * @param settings_navigation $navigation
 * @param context $context
 */
function local_hello_extend_settings_navigation(settings_navigation $navigation, context $context) {
    global $PAGE;
    
    // Only add to site context
    if ($context->contextlevel == CONTEXT_SYSTEM && has_capability('local/hello:view', $context)) {
        if ($settingnode = $navigation->find('localplugins', navigation_node::TYPE_SETTING)) {
            $url = new moodle_url('/local/hello/index.php');
            $node = navigation_node::create(
                get_string('pluginname', 'local_hello'),
                $url,
                navigation_node::TYPE_SETTING,
                null,
                'local_hello',
                new pix_icon('i/settings', '')
            );
            $settingnode->add_node($node);
        }
    }
}