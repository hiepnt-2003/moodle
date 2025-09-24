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
 * Restore definition for Hello World block
 *
 * @package    block_helloworld
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Specialised restore task for the helloworld block
 */
class restore_helloworld_block_task extends restore_block_task {

    /**
     * Define my settings
     */
    protected function define_my_settings() {
        // No specific settings for this block
    }

    /**
     * Define my steps
     */
    protected function define_my_steps() {
        // Hello World block doesn't need complex restore steps
        // The block configuration is automatically restored by Moodle
    }

    /**
     * File areas this block manages
     */
    public function get_fileareas() {
        return array(); // No file areas for this block
    }

    /**
     * Encoded attributes that need special handling
     */
    public function get_configdata_encoded_attributes() {
        return array(); // No encoded attributes in our config
    }

    /**
     * Decode content links after restore
     *
     * @param string $content content to decode
     * @return string decoded content
     */
    static public function decode_content_links($content) {
        // No special content links to decode for this simple block
        return $content;
    }
}