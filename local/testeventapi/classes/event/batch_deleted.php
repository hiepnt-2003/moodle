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
 * Batch deleted event.
 *
 * @package    local_testeventapi
 * @copyright  2025 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_testeventapi\event;

/**
 * Event fired when a batch is deleted.
 *
 * @package    local_testeventapi
 * @copyright  2025 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class batch_deleted extends \core\event\base {

    /**
     * Initialize the event.
     */
    protected function init() {
        $this->data['crud'] = 'd';
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->data['objecttable'] = 'local_testeventapi_batches';
    }

    /**
     * Get event name.
     *
     * @return string
     */
    public static function get_name() {
        return 'Batch deleted';
    }

    /**
     * Get event description.
     *
     * @return string
     */
    public function get_description() {
        return "Đợt học '{$this->other['name']}' đã được xóa (ID: {$this->objectid}).";
    }

    /**
     * Get event URL.
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/local/testeventapi/index.php');
    }

    /**
     * Get event objectid mapping.
     *
     * @return array
     */
    public static function get_objectid_mapping() {
        return ['db' => 'local_testeventapi_batches', 'restore' => 'local_testeventapi_batch'];
    }

    /**
     * Validate the event data.
     *
     * @throws \coding_exception
     */
    protected function validate_data() {
        parent::validate_data();

        if (!isset($this->other['name'])) {
            throw new \coding_exception('The \'name\' value must be set in other.');
        }

        if (!isset($this->other['start_date'])) {
            throw new \coding_exception('The \'start_date\' value must be set in other.');
        }
    }
}