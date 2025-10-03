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
 * Web service external functions and service definitions.
 *
 * @package    local_coursecopier
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// We defined the web service functions to install.
$functions = [
    'local_coursecopier_copy_course' => [
        'classname'   => 'local_coursecopier_external',
        'methodname'  => 'copy_course',
        'classpath'   => 'local/coursecopier/externallib.php',
        'description' => 'Copy a course with new details (shortname, fullname, dates)',
        'type'        => 'write',
        'ajax'        => true,
        'capabilities'=> 'moodle/course:create,moodle/backup:backupcourse,moodle/restore:restorecourse',
    ],
    'local_coursecopier_get_available_courses' => [
        'classname'   => 'local_coursecopier_external',
        'methodname'  => 'get_available_courses',
        'classpath'   => 'local/coursecopier/externallib.php',
        'description' => 'Get list of available courses for copying',
        'type'        => 'read',
        'ajax'        => true,
        'capabilities'=> 'moodle/course:view',
    ],
];

// We define the services to install as pre-build services. A pre-build service is not editable by administrator.
$services = [
    'Course Copier Service' => [
        'functions' => [
            'local_coursecopier_copy_course',
            'local_coursecopier_get_available_courses'
        ],
        'restrictedusers' => 0,
        'enabled' => 1,
        'shortname' => 'coursecopier',
        'downloadfiles' => 0,
        'uploadfiles' => 0,
    ],
];