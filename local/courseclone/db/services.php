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
 * @package    local_courseclone
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Define the web service functions.
$functions = [
    'local_courseclone_clone_course' => [
        'classname'   => 'local_courseclone_external',
        'methodname'  => 'clone_course',
        'classpath'   => 'local/courseclone/externallib.php',
        'description' => 'Clone a course with new details, keeping the same category',
        'type'        => 'write',
        'capabilities' => 'moodle/course:create,moodle/backup:backupcourse,moodle/restore:restorecourse',
    ],
];

// Define the services.
$services = [
    'Course Clone Service' => [
        'functions' => ['local_courseclone_clone_course'],
        'restrictedusers' => 0,
        'enabled' => 1,
        'shortname' => 'courseclone_service',
        'downloadfiles' => 0,
        'uploadfiles' => 0,
    ],
];