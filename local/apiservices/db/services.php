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
 * @package    local_apiservices
 * @copyright  2025 API Services Team
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Define the web service functions.
$functions = [
    'local_apiservices_copy_course' => [
        'classname'   => 'local_apiservices_external',
        'methodname'  => 'copy_course',
        'classpath'   => 'local/apiservices/externallib.php',
        'description' => 'Copy a course with new details (fullname, shortname, startdate, enddate)',
        'type'        => 'write',
        'capabilities' => 'moodle/course:create',
        'ajax'        => true,
    ],
    'local_apiservices_create_user' => [
        'classname'   => 'local_apiservices_external',
        'methodname'  => 'create_user',
        'classpath'   => 'local/apiservices/externallib.php',
        'description' => 'Create a new user with specified details',
        'type'        => 'write',
        'capabilities' => 'moodle/user:create',
        'ajax'        => true,
    ],
];

// Define the services.
$services = [
    'API Services' => [
        'functions' => [
            'local_apiservices_copy_course',
            'local_apiservices_create_user',
        ],
        'restrictedusers' => 0,
        'enabled' => 1,
        'shortname' => 'apiservices',
        'downloadfiles' => 0,
        'uploadfiles' => 0,
    ],
];
