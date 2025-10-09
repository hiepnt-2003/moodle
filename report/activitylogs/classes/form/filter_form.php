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
        $mform->addGroup($radioarray, 'filtertypegroup', '', array(' '), false);
        $mform->setDefault('filtertype', 'user');

        // User selection with autocomplete (shown when filter by user is selected)
        // Load tất cả users để hiển thị khi click
        $initialusers = $DB->get_records_sql("
            SELECT id, firstname, lastname, email 
            FROM {user} 
            WHERE deleted = 0 AND id > 1
            ORDER BY firstname, lastname
        ");
        
        $useroptions = array(0 => get_string('allusers', 'report_activitylogs'));
        foreach ($initialusers as $user) {
            $useroptions[$user->id] = fullname($user) . ' (' . $user->email . ')';
        }
        
        $options = array(
            'ajax' => 'core_user/form_user_selector',
            'multiple' => false,
            'noselectionstring' => get_string('allusers', 'report_activitylogs'),
            'valuehtmlcallback' => function($value) {
                global $DB, $OUTPUT;
                
                if (empty($value)) {
                    return get_string('allusers', 'report_activitylogs');
                }
                
                $user = $DB->get_record('user', array('id' => $value), 'id, firstname, lastname, email');
                if ($user) {
                    $username = fullname($user);
                    return $OUTPUT->render_from_template('core/user_selector_suggestion', [
                        'fullname' => $username,
                        'email' => $user->email
                    ]);
                }
                return '';
            }
        );
        
        $mform->addElement('autocomplete', 'userid', get_string('selectuser', 'report_activitylogs'), $useroptions, $options);
        $mform->setType('userid', PARAM_INT);
        $mform->hideIf('userid', 'filtertype', 'eq', 'course');
        
        // Course selection with autocomplete (shown when filter by course is selected)
        // Load tất cả courses để hiển thị khi click
        $initialcourses = $DB->get_records_sql("
            SELECT id, fullname 
            FROM {course} 
            WHERE id > 1
            ORDER BY fullname
        ");
        
        $courseoptionsdata = array(0 => get_string('allcourses', 'report_activitylogs'));
        foreach ($initialcourses as $course) {
            $courseoptionsdata[$course->id] = format_string($course->fullname);
        }
        
        $courseoptions = array(
            'ajax' => 'core_course/form_course_selector',
            'multiple' => false,
            'noselectionstring' => get_string('allcourses', 'report_activitylogs'),
            'valuehtmlcallback' => function($value) {
                global $DB;
                
                if (empty($value)) {
                    return get_string('allcourses', 'report_activitylogs');
                }
                
                $course = $DB->get_record('course', array('id' => $value), 'id, fullname');
                if ($course) {
                    return format_string($course->fullname);
                }
                return '';
            }
        );
        
        $mform->addElement('autocomplete', 'courseid', get_string('selectcourse', 'report_activitylogs'), $courseoptionsdata, $courseoptions);
        $mform->setType('courseid', PARAM_INT);
        $mform->hideIf('courseid', 'filtertype', 'eq', 'user');

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
