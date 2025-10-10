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
 * Filter form for Activity Logs report
 *
 * @package    report_activitylogs
 * @copyright  2025
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_activitylogs\form;

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");

class filter_form extends \moodleform {

    /**
     * Form definition
     */
    public function definition() {
        global $DB;
        
        $mform = $this->_form;

        // Radio buttons to choose filter type
        $radioarray = array();
        $radioarray[] = $mform->createElement('radio', 'filtertype', '', get_string('filterbyuser', 'report_activitylogs'), 'user');
        $radioarray[] = $mform->createElement('radio', 'filtertype', '', get_string('filterbycourse', 'report_activitylogs'), 'course');
        $mform->addGroup($radioarray, 'filtertypegroup', get_string('filterby', 'report_activitylogs'), array(' '), false);
        $mform->setDefault('filtertype', 'user');

        // User selection with autocomplete - Cho phép chọn nhiều users
        // Load tất cả users để hiển thị khi click
        $allusers = $DB->get_records_sql("
            SELECT id, firstname, lastname, firstnamephonetic, lastnamephonetic, middlename, alternatename, email 
            FROM {user} 
            WHERE deleted = 0 AND id > 1
            ORDER BY firstname, lastname
        ");
        
        $useroptions = array();
        foreach ($allusers as $user) {
            $useroptions[$user->id] = fullname($user) . ' (' . $user->email . ')';
        }
        
        $userselectoptions = array(
            'multiple' => true,  // Cho phép chọn nhiều
            'noselectionstring' => get_string('allusers', 'report_activitylogs'),
            'placeholder' => get_string('selectuser', 'report_activitylogs')
        );
        
        $mform->addElement('autocomplete', 'userids', get_string('selectuser', 'report_activitylogs'), $useroptions, $userselectoptions);
        $mform->addHelpButton('userids', 'selectuser', 'report_activitylogs');
        // Hiển thị user selection chỉ khi chọn filter by user
        $mform->hideIf('userids', 'filtertype', 'eq', 'course');
        
        // Course selection with autocomplete - Cho phép chọn nhiều courses
        // Load tất cả courses để hiển thị khi click
        $allcourses = $DB->get_records_sql("
            SELECT id, fullname 
            FROM {course} 
            WHERE id > 1
            ORDER BY fullname
        ");
        
        $courseoptions = array();
        foreach ($allcourses as $course) {
            $courseoptions[$course->id] = format_string($course->fullname);
        }
        
        $courseselectoptions = array(
            'multiple' => true,  // Cho phép chọn nhiều
            'noselectionstring' => get_string('allcourses', 'report_activitylogs'),
            'placeholder' => get_string('selectcourse', 'report_activitylogs')
        );
        
        $mform->addElement('autocomplete', 'courseids', get_string('selectcourse', 'report_activitylogs'), $courseoptions, $courseselectoptions);
        $mform->addHelpButton('courseids', 'selectcourse', 'report_activitylogs');
        // Hiển thị course selection chỉ khi chọn filter by course
        $mform->hideIf('courseids', 'filtertype', 'eq', 'user');

        // Date from
        $mform->addElement('date_selector', 'datefrom', get_string('datefrom', 'report_activitylogs'), array('optional' => false));
        $mform->setDefault('datefrom', strtotime('-7 days'));

        // Date to
        $mform->addElement('date_selector', 'dateto', get_string('dateto', 'report_activitylogs'), array('optional' => false));
        $mform->setDefault('dateto', time());

        // Submit button
        $this->add_action_buttons(false, get_string('viewlogs', 'report_activitylogs'));
    }

    /**
     * Validate form data
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if ($data['datefrom'] > $data['dateto']) {
            $errors['dateto'] = get_string('invaliddate', 'error');
        }

        return $errors;
    }
}
