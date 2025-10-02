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
 * Report filter form.
 *
 * @package    report_userreport
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_userreport\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Filter form for user activity report.
 *
 * @package    report_userreport
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filter_form extends \moodleform {

    /**
     * Define the form elements.
     */
    public function definition() {
        global $DB;

        $mform = $this->_form;

        // Header.
        $mform->addElement('header', 'filtersheader', get_string('filters', 'core'));

        // User selection.
        $users = $DB->get_records_sql("
            SELECT u.id, CONCAT(u.firstname, ' ', u.lastname) as fullname 
            FROM {user} u 
            WHERE u.deleted = 0 AND u.id > 1 
            ORDER BY u.firstname, u.lastname
        ");
        $useroptions = [0 => get_string('selectuser', 'report_userreport')];
        foreach ($users as $user) {
            $useroptions[$user->id] = $user->fullname;
        }
        $mform->addElement('select', 'userid', get_string('selectuser', 'report_userreport'), $useroptions);
        $mform->setType('userid', PARAM_INT);
        $mform->addRule('userid', null, 'required', null, 'client');

        // Date range group.
        $dategroup = [];
        $dategroup[] = $mform->createElement('date_selector', 'startdate', get_string('startdate', 'report_userreport'));
        $dategroup[] = $mform->createElement('static', 'to', '', ' ' . get_string('to', 'core') . ' ');
        $dategroup[] = $mform->createElement('date_selector', 'enddate', get_string('enddate', 'report_userreport'));
        $mform->addGroup($dategroup, 'daterange', get_string('daterange', 'core'), ' ', false);

        $mform->setDefault('startdate', strtotime('-7 days'));
        $mform->setDefault('enddate', time());
        $mform->setType('startdate', PARAM_INT);
        $mform->setType('enddate', PARAM_INT);

        // Course selection.
        $courses = $DB->get_records('course', null, 'fullname ASC', 'id, fullname');
        $courseoptions = [0 => get_string('allcourses', 'report_userreport')];
        foreach ($courses as $course) {
            $courseoptions[$course->id] = $course->fullname;
        }
        $mform->addElement('select', 'courseid', get_string('selectcourse', 'report_userreport'), $courseoptions);
        $mform->setType('courseid', PARAM_INT);

        // Submit buttons.
        $this->add_action_buttons(false, get_string('generatereport', 'report_userreport'));
    }

    /**
     * Validate the form data.
     *
     * @param array $data Array of form data
     * @param array $files Array of uploaded files
     * @return array Array of errors
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if (!empty($data['startdate']) && !empty($data['enddate'])) {
            if ($data['startdate'] > $data['enddate']) {
                $errors['daterange'] = get_string('invaliddaterange', 'report_userreport');
            }
        }

        return $errors;
    }
}