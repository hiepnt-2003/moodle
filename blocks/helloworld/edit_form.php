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
 * Form for editing Hello World block instances.
 *
 * @package    block_helloworld
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class block_helloworld_edit_form extends block_edit_form {

    /**
     * Defines the configuration form for the block
     *
     * @param object $mform the form being built
     */
    protected function specific_definition($mform) {

        // Section header title according to language file.
        $mform->addElement('header', 'config_header', get_string('blocksettings', 'block'));

        // Block title field
        $mform->addElement('text', 'config_title', get_string('blocktitle', 'block_helloworld'));
        $mform->setDefault('config_title', get_string('defaulttitle', 'block_helloworld'));
        $mform->setType('config_title', PARAM_TEXT);
        $mform->addHelpButton('config_title', 'blocktitle', 'block_helloworld');

        // Message textarea with attributes
        $attributes = array('wrap' => 'virtual', 'rows' => '5', 'cols' => '50');
        $mform->addElement('textarea', 'config_message', get_string('message', 'block_helloworld'), $attributes);
        $mform->setDefault('config_message', get_string('defaultmessage', 'block_helloworld'));
        $mform->setType('config_message', PARAM_TEXT);
        $mform->addHelpButton('config_message', 'message', 'block_helloworld');

        // Show date checkbox
        $mform->addElement('advcheckbox', 'config_showdate', get_string('showdate', 'block_helloworld'));
        $mform->setDefault('config_showdate', 1);
        $mform->addHelpButton('config_showdate', 'showdate', 'block_helloworld');
    }

    /**
     * Validation function for the form
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK (true allowed for backwards compatibility too).
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // Validate title is not empty
        if (empty(trim($data['config_title']))) {
            $errors['config_title'] = get_string('required');
        }

        // Validate message is not empty
        if (empty(trim($data['config_message']))) {
            $errors['config_message'] = get_string('required');
        }

        return $errors;
    }
}