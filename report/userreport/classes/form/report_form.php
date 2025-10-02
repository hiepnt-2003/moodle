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
 * User Activity Report form.
 *
 * @package    report_userreport
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Form for user activity report filters.
 */
class report_userreport_form extends moodleform {

    /**
     * Form definition.
     */
    public function definition() {
        global $DB;

        $mform = $this->_form;

        // User selection.
        $users = $DB->get_records_sql("
            SELECT u.id, CONCAT(u.firstname, ' ', u.lastname) as fullname 
            FROM {user} u 
            WHERE u.deleted = 0 AND u.id > 1 
            ORDER BY u.firstname, u.lastname
        ");
        $useroptions = array();
        $useroptions[0] = get_string('selectuser', 'report_userreport');
        foreach ($users as $user) {
            $useroptions[$user->id] = $user->fullname;
        }
        $mform->addElement('select', 'userid', get_string('selectuser', 'report_userreport'), $useroptions);
        $mform->setType('userid', PARAM_INT);
        $mform->addRule('userid', null, 'required', null, 'client');

        // Start date.
        $mform->addElement('date_selector', 'startdate', get_string('startdate', 'report_userreport'));
        $mform->setDefault('startdate', strtotime('-7 days'));
        $mform->setType('startdate', PARAM_INT);

        // End date.
        $mform->addElement('date_selector', 'enddate', get_string('enddate', 'report_userreport'));
        $mform->setDefault('enddate', time());
        $mform->setType('enddate', PARAM_INT);

        // Course selection.
        $courses = $DB->get_records('course', null, 'fullname ASC', 'id, fullname');
        $courseoptions = array();
        $courseoptions[0] = get_string('allcourses', 'report_userreport');
        foreach ($courses as $course) {
            $courseoptions[$course->id] = $course->fullname;
        }
        $mform->addElement('select', 'courseid', get_string('selectcourse', 'report_userreport'), $courseoptions);
        $mform->setType('courseid', PARAM_INT);

        // Submit button.
        $this->add_action_buttons(false, get_string('generatereport', 'report_userreport'));
    }

    /**
     * Form validation.
     *
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if ($data['startdate'] > $data['enddate']) {
            $errors['enddate'] = 'Ngày kết thúc phải sau ngày bắt đầu';
        }

        return $errors;
    }
}