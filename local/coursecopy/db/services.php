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
 * Web service definitions for course copy using RESTful protocol.
 *
 * @package    local_coursecopy
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
    'local_coursecopy_copy_course' => [
        'classname' => 'local_coursecopy_external',
        'methodname' => 'copy_course',
        'classpath' => 'local/webservice_coursecopy/externallib.php',
        'description' => 'Copy a course with new parameters using RESTful protocol',
        'type' => 'write',
        'ajax' => true,
        'capabilities' => 'moodle/course:create,moodle/backup:backupcourse,moodle/restore:restorecourse',
    ],
];

$services = [
    'Course Copy RESTful Service' => [
        'functions' => ['local_coursecopy_copy_course'],
        'restrictedusers' => 0,
        'enabled' => 1,
        'shortname' => 'coursecopy_restful',
        'downloadfiles' => 0,
        'uploadfiles' => 0,
    ],
];
