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
 * @package    local_webservice
 * @copyright  2025 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Define the web service functions.
$functions = [
    'local_webservice_clone_course' => [
        'classname'   => 'local_webservice_external',
        'methodname'  => 'clone_course',
        'classpath'   => 'local/webservice/externallib.php',
        'description' => 'Clone a course with new details',
        'type'        => 'write',
        'capabilities' => 'moodle/course:create,moodle/backup:backupcourse,moodle/restore:restorecourse',
    ],
];

// Define the services.
$services = [
    'Course Clone Service' => [
        'functions' => ['local_webservice_clone_course'],
        'restrictedusers' => 0,
        'enabled' => 1,
        'shortname' => 'course_clone_service',
        'downloadfiles' => 0,
        'uploadfiles' => 0,
    ],
];