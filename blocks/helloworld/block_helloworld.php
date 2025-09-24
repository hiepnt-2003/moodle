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
 * Hello World block
 *
 * @package    block_helloworld
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class block_helloworld extends block_base {

    /**
     * Initialize the block
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_helloworld');
    }

    /**
     * Allow the block to have a configuration page
     */
    public function has_config() {
        return true;
    }

    /**
     * Allow multiple instances of this block
     */
    public function instance_allow_multiple() {
        return true;
    }

    /**
     * Locations where block can be displayed
     */
    public function applicable_formats() {
        return array(
            'course-view' => true,
            'site' => true,
            'mod' => true,
            'my' => true
        );
    }

    /**
     * Set the initial content for the block
     */
    public function get_content() {
        global $USER, $COURSE;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();

        // Get configuration settings
        $customtitle = '';
        $custommessage = '';
        
        if (!empty($this->config->title)) {
            $customtitle = $this->config->title;
        } else {
            $customtitle = get_string('defaulttitle', 'block_helloworld');
        }

        if (!empty($this->config->message)) {
            $custommessage = $this->config->message;
        } else {
            $custommessage = get_string('defaultmessage', 'block_helloworld');
        }

        // Replace placeholders
        $custommessage = str_replace('{username}', fullname($USER), $custommessage);
        $custommessage = str_replace('{coursename}', $COURSE->fullname, $custommessage);

        // Format current date
        $currentdate = userdate(time(), get_string('strftimedatefullshort'));

        // Prepare template data
        $templatedata = array(
            'title' => $customtitle,
            'message' => $custommessage,
            'showdate' => !empty($this->config->showdate),
            'currentdate' => $currentdate
        );

        // Render using template
        $renderer = $this->page->get_renderer('block_helloworld');
        $this->content->text = $renderer->render_content($templatedata);
        $this->content->footer = '';

        return $this->content;
    }

    /**
     * Serialize and store config data
     */
    public function instance_config_save($data, $nolongerused = false) {
        $config = clone($data);
        // Clean and format the message
        $config->message = clean_text($config->message);
        parent::instance_config_save($config, $nolongerused);
    }
}