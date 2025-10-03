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
 * Course Selection form class
 *
 * @package    block_mydata
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_mydata\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/blocks/mydata/lib.php');

/**
 * Course selection form class
 */
class course_selection_form extends \moodleform {
    
    /**
     * Form definition
     */
    public function definition() {
        $mform = $this->_form;
        
        // Get course options from lib.php
        $course_options = block_mydata_get_visible_courses();
        
        // Add autocomplete element for multiple course selection
        $mform->addElement('autocomplete', 'courseids', get_string('select_courses', 'block_mydata'), $course_options, array(
            'multiple' => true,
            'placeholder' => get_string('course_placeholder', 'block_mydata'),
            'casesensitive' => false,
            'showsuggestions' => true
        ));
        $mform->addRule('courseids', get_string('select_course_required', 'block_mydata'), 'required', null, 'client');
        $mform->addHelpButton('courseids', 'courseselection', 'block_mydata');
        
        // Submit button
        $this->add_action_buttons(false, get_string('view_report', 'block_mydata'));
    }
}