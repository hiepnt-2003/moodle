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

        // Select user (exclude deleted users)
        $users = $DB->get_records_sql("
            SELECT id, CONCAT(firstname, ' ', lastname, ' (', email, ')') as fullname 
            FROM {user} 
            WHERE deleted = 0 AND id > 1
            ORDER BY firstname, lastname
        ");
        
        $useroptions = array(0 => get_string('allusers', 'report_activitylogs'));
        foreach ($users as $user) {
            $useroptions[$user->id] = $user->fullname;
        }
        
        $mform->addElement('select', 'userid', get_string('selectuser', 'report_activitylogs'), $useroptions);
        $mform->setType('userid', PARAM_INT);

        // Select course
        $courses = $DB->get_records_menu('course', null, 'fullname', 'id, fullname');
        $courseoptions = array(0 => get_string('allcourses', 'report_activitylogs'));
        foreach ($courses as $courseid => $coursename) {
            $courseoptions[$courseid] = $coursename;
        }
        
        $mform->addElement('select', 'courseid', get_string('selectcourse', 'report_activitylogs'), $courseoptions);
        $mform->setType('courseid', PARAM_INT);

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
