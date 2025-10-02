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
 * Filter form for User Activity Log report.
 *
 * @package    report_useractivitylog
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_useractivitylog\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Filter form for user activity log report.
 */
class filter_form extends \moodleform {

    /**
     * Define the form.
     */
    public function definition() {
        global $DB;

        $mform = $this->_form;

        // Header
        $mform->addElement('header', 'filterheader', get_string('reporttitle', 'report_useractivitylog'));

        // User selection
        $users = $this->get_active_users();
        $mform->addElement('select', 'userid', get_string('selectuser', 'report_useractivitylog'), $users);
        $mform->addHelpButton('userid', 'selectuser', 'report_useractivitylog');
        $mform->addRule('userid', get_string('required'), 'required', null, 'client');

        // Date range
        $mform->addElement('date_selector', 'startdate', get_string('startdate', 'report_useractivitylog'));
        $mform->setDefault('startdate', strtotime('-1 week'));

        $mform->addElement('date_selector', 'enddate', get_string('enddate', 'report_useractivitylog'));
        $mform->setDefault('enddate', time());

        // Course selection
        $courses = $this->get_available_courses();
        $mform->addElement('select', 'courseid', get_string('selectcourse', 'report_useractivitylog'), $courses);
        $mform->addHelpButton('courseid', 'selectcourse', 'report_useractivitylog');

        // Action buttons
        $buttonarray = array();
        $buttonarray[] = $mform->createElement('submit', 'submitbutton', get_string('filterbutton', 'report_useractivitylog'));
        $buttonarray[] = $mform->createElement('cancel', 'resetbutton', get_string('resetfilter', 'report_useractivitylog'));
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
    }

    /**
     * Validation of form data.
     *
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if (!empty($data['startdate']) && !empty($data['enddate'])) {
            if ($data['startdate'] >= $data['enddate']) {
                $errors['enddate'] = get_string('daterangeerror', 'report_useractivitylog');
            }
        }

        return $errors;
    }

    /**
     * Get list of active users (not deleted).
     *
     * @return array
     */
    private function get_active_users() {
        global $DB;

        $users = array('' => get_string('selectuser', 'report_useractivitylog'));
        
        $sql = "SELECT id, firstname, lastname, username 
                FROM {user} 
                WHERE deleted = 0 AND confirmed = 1 
                ORDER BY firstname, lastname";
        
        $userrecords = $DB->get_records_sql($sql);
        
        foreach ($userrecords as $user) {
            $displayname = fullname($user) . ' (' . $user->username . ')';
            $users[$user->id] = $displayname;
        }

        return $users;
    }

    /**
     * Get list of available courses.
     *
     * @return array
     */
    private function get_available_courses() {
        global $DB;

        $courses = array('' => get_string('allcourses', 'report_useractivitylog'));
        
        $sql = "SELECT id, fullname, shortname 
                FROM {course} 
                WHERE visible = 1 
                ORDER BY fullname";
        
        $courserecords = $DB->get_records_sql($sql);
        
        foreach ($courserecords as $course) {
            $displayname = $course->fullname . ' (' . $course->shortname . ')';
            $courses[$course->id] = $displayname;
        }

        return $courses;
    }
}