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
 * External web service functions for user creation.
 *
 * @package    local_usercreation
 * @copyright  2025 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/user/lib.php');

/**
 * External functions for user creation.
 */
class local_usercreation_external extends external_api {

    /**
     * Describes the parameters for create_user function.
     *
     * @return external_function_parameters
     */
    public static function create_user_parameters() {
        return new external_function_parameters([
            'username' => new external_value(PARAM_USERNAME, 'Username for the new user'),
            'firstname' => new external_value(PARAM_TEXT, 'First name of the user'),
            'lastname' => new external_value(PARAM_TEXT, 'Last name of the user'),
            'email' => new external_value(PARAM_EMAIL, 'Email address of the user'),
            'createpassword' => new external_value(PARAM_BOOL, 'Whether to create a password automatically'),
            'password' => new external_value(PARAM_TEXT, 'Password for the user (required if createpassword is false)', VALUE_DEFAULT, ''),
        ]);
    }

    /**
     * Create a new user.
     *
     * @param string $username Username
     * @param string $firstname First name
     * @param string $lastname Last name
     * @param string $email Email address
     * @param bool $createpassword Whether to create password automatically
     * @param string $password Password (if not auto-generating)
     * @return array Result with status, id, and message
     */
    public static function create_user($username, $firstname, $lastname, $email, $createpassword, $password = '') {
        global $DB, $CFG;

        // Validate parameters.
        $params = self::validate_parameters(self::create_user_parameters(), [
            'username' => $username,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email,
            'createpassword' => $createpassword,
            'password' => $password,
        ]);

        // Validate context and capabilities.
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('moodle/user:create', $context);

        try {
            // Step 1: Validate input parameters.
            $validation_result = self::validate_user_parameters($params);
            if (!$validation_result['success']) {
                return [
                    'status' => 'error',
                    'id' => 0,
                    'message' => $validation_result['message']
                ];
            }

            // Step 2: Check if username already exists.
            if ($DB->record_exists('user', ['username' => $params['username']])) {
                return [
                    'status' => 'error',
                    'id' => 0,
                    'message' => get_string('error_usernameinuse', 'local_usercreation', $params['username'])
                ];
            }

            // Step 3: Check if email already exists.
            if ($DB->record_exists('user', ['email' => $params['email']])) {
                return [
                    'status' => 'error',
                    'id' => 0,
                    'message' => get_string('error_emailinuse', 'local_usercreation', $params['email'])
                ];
            }

            // Step 4: Prepare user data.
            $user = new stdClass();
            $user->username = $params['username'];
            $user->firstname = $params['firstname'];
            $user->lastname = $params['lastname'];
            $user->email = $params['email'];
            $user->confirmed = 1;
            $user->mnethostid = $CFG->mnet_localhost_id;
            $user->auth = 'manual';
            $user->timecreated = time();
            $user->timemodified = time();

            // Step 5: Handle password.
            if ($params['createpassword']) {
                // Generate random password.
                $user->password = hash_internal_user_password(self::generate_random_password());
            } else {
                if (empty($params['password'])) {
                    return [
                        'status' => 'error',
                        'id' => 0,
                        'message' => get_string('error_passwordrequired', 'local_usercreation')
                    ];
                }
                // Validate password strength.
                if (!self::validate_password($params['password'])) {
                    return [
                        'status' => 'error',
                        'id' => 0,
                        'message' => get_string('error_passwordweak', 'local_usercreation')
                    ];
                }
                $user->password = hash_internal_user_password($params['password']);
            }

            // Step 6: Create the user.
            $user->id = $DB->insert_record('user', $user);

            if (!$user->id) {
                return [
                    'status' => 'error',
                    'id' => 0,
                    'message' => get_string('error_usercreationfailed', 'local_usercreation')
                ];
            }

            // Step 7: Trigger user created event.
            \core\event\user_created::create_from_userid($user->id)->trigger();

            return [
                'status' => 'success',
                'id' => $user->id,
                'message' => get_string('success_usercreated', 'local_usercreation')
            ];

        } catch (Exception $e) {
            return [
                'status' => 'error',
                'id' => 0,
                'message' => 'User creation failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Validate user parameters.
     *
     * @param array $params Parameters to validate
     * @return array Validation result
     */
    private static function validate_user_parameters($params) {
        // Validate required fields.
        if (empty(trim($params['username'])) || empty(trim($params['firstname'])) || 
            empty(trim($params['lastname'])) || empty(trim($params['email']))) {
            return [
                'success' => false,
                'message' => get_string('error_requiredfields', 'local_usercreation')
            ];
        }

        // Validate username format.
        if (!preg_match('/^[a-zA-Z0-9._-]+$/', $params['username'])) {
            return [
                'success' => false,
                'message' => get_string('error_invalidusername', 'local_usercreation')
            ];
        }

        // Validate email format.
        if (!filter_var($params['email'], FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => get_string('error_invalidemail', 'local_usercreation')
            ];
        }

        return ['success' => true];
    }

    /**
     * Generate random password.
     *
     * @return string Random password
     */
    private static function generate_random_password() {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        $password = '';
        for ($i = 0; $i < 12; $i++) {
            $password .= $chars[rand(0, strlen($chars) - 1)];
        }
        return $password;
    }

    /**
     * Validate password strength.
     *
     * @param string $password Password to validate
     * @return bool True if password is strong enough
     */
    private static function validate_password($password) {
        // At least 8 characters.
        if (strlen($password) < 8) {
            return false;
        }

        // At least one lowercase letter.
        if (!preg_match('/[a-z]/', $password)) {
            return false;
        }

        // At least one uppercase letter.
        if (!preg_match('/[A-Z]/', $password)) {
            return false;
        }

        // At least one digit.
        if (!preg_match('/[0-9]/', $password)) {
            return false;
        }

        return true;
    }

    /**
     * Describes the return value for create_user function.
     *
     * @return external_single_structure
     */
    public static function create_user_returns() {
        return new external_single_structure([
            'status' => new external_value(PARAM_TEXT, 'Status: success or error'),
            'id' => new external_value(PARAM_INT, 'ID of the created user (0 if error)'),
            'message' => new external_value(PARAM_TEXT, 'Success message or error description'),
        ]);
    }
}