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
 * RESTful JSON API endpoint for Course Copier plugin.
 * 
 * This file provides a JSON-friendly REST API wrapper around Moodle web services.
 * Usage: POST /local/coursecopier/api.php with JSON body
 *
 * @package    local_coursecopier
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->libdir . '/externallib.php');
require_once('externallib.php');

// Set headers for JSON API
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle CORS preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only allow POST method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'id' => 0,
        'message' => 'Only POST method allowed'
    ]);
    exit();
}

/**
 * Send JSON error response
 */
function send_error($message, $code = 400) {
    http_response_code($code);
    echo json_encode([
        'status' => 'error',
        'id' => 0,
        'message' => $message
    ]);
    exit();
}

/**
 * Send JSON success response
 */
function send_success($data) {
    http_response_code(200);
    echo json_encode($data);
    exit();
}

/**
 * Extract token from Authorization header or JSON body
 */
function get_token($json_data) {
    // Try Authorization header first
    $headers = getallheaders();
    if (isset($headers['Authorization'])) {
        $auth_header = $headers['Authorization'];
        if (preg_match('/Bearer\s+(.+)/', $auth_header, $matches)) {
            return $matches[1];
        }
    }
    
    // Fall back to token in JSON body
    if (isset($json_data['wstoken'])) {
        return $json_data['wstoken'];
    }
    
    return null;
}

/**
 * Validate token and get user
 */
function validate_token($token) {
    global $DB, $USER;
    
    if (empty($token)) {
        send_error('Token is required', 401);
    }
    
    // Get token record
    $tokenrecord = $DB->get_record('external_tokens', ['token' => $token]);
    if (!$tokenrecord) {
        send_error('Invalid token', 401);
    }
    
    // Check if token is expired
    if ($tokenrecord->validuntil && $tokenrecord->validuntil < time()) {
        send_error('Token expired', 401);
    }
    
    // Get user and set session
    $user = $DB->get_record('user', ['id' => $tokenrecord->userid]);
    if (!$user) {
        send_error('User not found', 401);
    }
    
    // Set user session
    complete_user_login($user);
    
    return $user;
}

try {
    // Get JSON input
    $json_input = file_get_contents('php://input');
    if (empty($json_input)) {
        send_error('JSON body is required');
    }
    
    $json_data = json_decode($json_input, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        send_error('Invalid JSON: ' . json_last_error_msg());
    }
    
    // Get and validate token
    $token = get_token($json_data);
    $user = validate_token($token);
    
    // Get function name
    $function_name = $json_data['wsfunction'] ?? '';
    if (empty($function_name)) {
        send_error('wsfunction parameter is required');
    }
    
    // Route to appropriate function
    switch ($function_name) {
        case 'local_coursecopier_copy_course':
            $result = local_coursecopier_external::copy_course(
                $json_data['shortname_clone'] ?? '',
                $json_data['fullname'] ?? '',
                $json_data['shortname'] ?? '',
                $json_data['startdate'] ?? 0,
                $json_data['enddate'] ?? 0
            );
            break;
            
        case 'local_coursecopier_get_available_courses':
            $result = local_coursecopier_external::get_available_courses(
                $json_data['categoryid'] ?? 0
            );
            break;
            
        default:
            send_error('Unknown function: ' . $function_name);
    }
    
    // Send success response
    send_success($result);
    
} catch (Exception $e) {
    // Handle any exceptions
    send_error('Server error: ' . $e->getMessage(), 500);
}