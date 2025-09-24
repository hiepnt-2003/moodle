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
 * Library functions for Hello World block
 *
 * @package    block_helloworld
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * This function extends the settings navigation block for the site.
 *
 * It is safe to rely on PAGE here as we will only ever be within the module
 * context when this is called
 *
 * @param settings_navigation $settings
 * @param navigation_node $helloworldnode
 */
function block_helloworld_extend_settings_navigation($settings, $helloworldnode) {
    // This function can be used to add additional navigation items
    // Currently not implementing any additional navigation
}

/**
 * Return the plugin config settings for external functions.
 *
 * @return stdClass the configs for both the block instance and plugin
 * @since Moodle 3.8
 */
function block_helloworld_get_fontawesome_icon_map() {
    return [
        'block_helloworld:icon' => 'fa-hand-o-right',
    ];
}