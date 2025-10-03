<?php
/**
 * Simple RESTful Course Clone Server
 * Uses existing external functions - No complex dependencies
 */

require('../config.php');
require_once($CFG->dirroot . '/local/courseclone/externallib.php');

// Set headers for RESTful API
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle CORS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

/**
 * Simple RESTful Handler
 */
try {
    // Get Bearer token
    $token = null;
    $headers = function_exists('getallheaders') ? getallheaders() : [];
    
    foreach ($headers as $key => $value) {
        if (strtolower($key) === 'authorization' && preg_match('/Bearer\s+(.*)$/i', $value, $matches)) {
            $token = trim($matches[1]);
            break;
        }
    }
    
    // Get request data
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    // If no Bearer token, try to get from body
    if (!$token && isset($data['wstoken'])) {
        $token = $data['wstoken'];
    }
    
    if (!$token) {
        throw new Exception('No token provided');
    }
    
    // Authenticate token
    global $DB, $USER;
    $tokenrecord = $DB->get_record('external_tokens', array('token' => $token));
    
    if (!$tokenrecord) {
        throw new Exception('Invalid token - token not found');
    }
    
    if ($tokenrecord->validuntil > 0 && $tokenrecord->validuntil < time()) {
        throw new Exception('Token expired');
    }
    
    // Set user
    $USER = $DB->get_record('user', array('id' => $tokenrecord->userid));
    if (!$USER) {
        throw new Exception('User not found');
    }
    
    // Get function
    $wsfunction = $data['wsfunction'] ?? null;
    if (!$wsfunction) {
        throw new Exception('No function specified');
    }
    
    // Create external instance
    $external = new local_courseclone_external();
    
    // Route to function
    switch ($wsfunction) {
        case 'local_courseclone_get_course_list':
            $categoryid = (int)($data['categoryid'] ?? 0);
            $visible = (bool)($data['visible'] ?? true);
            $result = $external::get_course_list($categoryid, $visible);
            break;
            
        case 'local_courseclone_get_clone_status':
            $courseid = (int)($data['courseid'] ?? 0);
            if (!$courseid) {
                throw new Exception('Course ID required');
            }
            $result = $external::get_clone_status($courseid);
            break;
            
        case 'local_courseclone_clone_course':
            $shortname_clone = $data['shortname_clone'] ?? '';
            $fullname = $data['fullname'] ?? '';
            $shortname = $data['shortname'] ?? '';
            $startdate = (int)($data['startdate'] ?? 0);
            $enddate = (int)($data['enddate'] ?? 0);
            
            if (!$shortname_clone || !$fullname || !$shortname || !$startdate || !$enddate) {
                throw new Exception('Missing required parameters');
            }
            
            $result = $external::clone_course($shortname_clone, $fullname, $shortname, $startdate, $enddate);
            break;
            
        default:
            throw new Exception('Unknown function: ' . $wsfunction);
    }
    
    // Send success response
    http_response_code(200);
    echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    // Send error response
    http_response_code(400);
    echo json_encode(array(
        'exception' => 'webservice_access_exception',
        'errorcode' => 'invalidtoken',
        'message' => $e->getMessage(),
        'debuginfo' => ''
    ), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}
?>