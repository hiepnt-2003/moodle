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
 * English language strings for API Services plugin.
 *
 * @package    local_apiservices
 * @copyright  2025 API Services Team
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'API Services';
$string['apiservices:copycourse'] = 'Copy courses via web service';
$string['apiservices:createuser'] = 'Create users via web service';
$string['privacy:metadata'] = 'The API Services plugin does not store any personal data.';

// User Creation - Success messages.
$string['success_usercreated'] = 'User has been successfully created';

// User Creation - Error messages.
$string['error_usernameinuse'] = 'Username "{$a}" is already in use';
$string['error_emailinuse'] = 'Email "{$a}" is already in use';
$string['error_requiredfields'] = 'All required fields must be provided';
$string['error_invalidusername'] = 'Username contains invalid characters. Only letters, numbers, dots, underscores and hyphens are allowed';
$string['error_invalidemail'] = 'Invalid email format';
$string['error_passwordrequired'] = 'Password is required when createpassword is false';
$string['error_passwordweak'] = 'Password must be at least 8 characters long and contain at least one lowercase letter, one uppercase letter, and one digit';
$string['error_usercreationfailed'] = 'Failed to create user in database';
