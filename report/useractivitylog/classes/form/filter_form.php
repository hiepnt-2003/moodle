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
        $mform->addElement('header', 'filterheader', \get_string('logsheading', 'report_useractivitylog'));

        // Course selection (first, like standard logs)
        $courses = $this->get_available_courses();
        $mform->addElement('select', 'courseid', \get_string('course', 'report_useractivitylog'), $courses);
        $mform->setType('courseid', PARAM_INT);

        // User selection - allow multiple users or all users
        $users = $this->get_active_users();
        $mform->addElement('select', 'userid', \get_string('participants', 'report_useractivitylog'), $users);
        $mform->setType('userid', PARAM_INT);

        // Date selection - single date field like standard logs
        $mform->addElement('date_selector', 'date', \get_string('date', 'report_useractivitylog'));
        $mform->setDefault('date', time());

        // Activities (modules)
        $activities = $this->get_activities();
        $mform->addElement('select', 'modid', \get_string('activities', 'report_useractivitylog'), $activities);
        $mform->setType('modid', PARAM_TEXT);

        // Actions
        $actions = $this->get_actions();
        $mform->addElement('select', 'action', \get_string('actions', 'report_useractivitylog'), $actions);
        $mform->setType('action', PARAM_ALPHA);

        // Education level
        $edulevels = $this->get_education_levels();
        $mform->addElement('select', 'edulevel', \get_string('edulevel', 'report_useractivitylog'), $edulevels);
        $mform->setType('edulevel', PARAM_INT);

        // Origin
        $origins = array(
            '' => \get_string('all', 'report_useractivitylog'),
            'web' => 'Web',
            'ws' => 'Web service',
            'cli' => 'Command line',
            'restore' => 'Restore'
        );
        $mform->addElement('select', 'origin', \get_string('origin', 'report_useractivitylog'), $origins);
        $mform->setType('origin', PARAM_ALPHA);

        // Display options
        $mform->addElement('text', 'logsperpage', \get_string('logsperpage', 'report_useractivitylog'), array('size' => 5));
        $mform->setType('logsperpage', PARAM_INT);
        $mform->setDefault('logsperpage', 100);

        // Action buttons
        $buttonarray = array();
        $buttonarray[] = $mform->createElement('submit', 'submitbutton', \get_string('gettheselogs', 'report_useractivitylog'));
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

        if (!empty($data['logsperpage']) && ($data['logsperpage'] < 1 || $data['logsperpage'] > 5000)) {
            $errors['logsperpage'] = \get_string('invalidlogsperpage', 'report_useractivitylog');
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

        $users = array(0 => \get_string('allusers', 'report_useractivitylog'));
        
        $sql = "SELECT id, firstname, lastname, username 
                FROM {user} 
                WHERE deleted = 0 AND confirmed = 1 
                ORDER BY firstname, lastname";
        
        $userrecords = $DB->get_records_sql($sql);
        
        foreach ($userrecords as $user) {
            $displayname = fullname($user);
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

        $courses = array(0 => \get_string('allcourses'));
        
        $sql = "SELECT id, fullname, shortname 
                FROM {course} 
                WHERE visible = 1 
                ORDER BY fullname";
        
        $courserecords = $DB->get_records_sql($sql);
        
        foreach ($courserecords as $course) {
            $displayname = $course->fullname; // Use fullname directly to avoid format_string issues
            if ($course->shortname) {
                $displayname .= ' (' . $course->shortname . ')';
            }
            $courses[$course->id] = $displayname;
        }

        return $courses;
    }

    /**
     * Get list of activities/modules.
     *
     * @return array
     */
    private function get_activities() {
        global $DB;

        $activities = array('' => \get_string('allactivities', 'report_useractivitylog'));
        
        // Get all activity modules
        $modules = $DB->get_records('modules', array('visible' => 1), 'name');
        
        foreach ($modules as $module) {
            $activities[$module->name] = \get_string('pluginname', $module->name);
        }

        return $activities;
    }

    /**
     * Get list of actions.
     *
     * @return array
     */
    private function get_actions() {
        return array(
            '' => \get_string('allactions', 'report_useractivitylog'),
            'c' => \get_string('create', 'report_useractivitylog'),
            'r' => \get_string('view', 'report_useractivitylog'),
            'u' => \get_string('update', 'report_useractivitylog'), 
            'd' => \get_string('delete', 'report_useractivitylog')
        );
    }

    /**
     * Get education levels.
     *
     * @return array
     */
    private function get_education_levels() {
        return array(
            -1 => \get_string('alleducationlevels', 'report_useractivitylog'),
            1 => \get_string('educationlevelother', 'report_useractivitylog'),
            2 => \get_string('educationlevelparticipating', 'report_useractivitylog'),
            3 => \get_string('educationlevelteaching', 'report_useractivitylog')
        );
    }
}