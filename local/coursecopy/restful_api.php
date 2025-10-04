<?php
/**
 * Course Copy RESTful API Endpoint
 * 
 * Endpoint này sử dụng plugin webservice_restful để copy môn học
 * 
 * @package    local_coursecopy
 * @copyright  2025 Course Copy Team
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Prevent direct access and disable output buffering
define('NO_OUTPUT_BUFFERING', true);

require_once('../../config.php');
require_once($CFG->dirroot . '/local/coursecopy/externallib.php');

// Set RESTful API headers
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle CORS preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

/**
 * Main RESTful API Handler
 */
try {
    // Extract Bearer token from Authorization header
    $token = null;
    $headers = function_exists('getallheaders') ? getallheaders() : [];
    
    // Try to get token from Authorization header
    foreach ($headers as $key => $value) {
        if (strtolower($key) === 'authorization' && preg_match('/Bearer\s+(.*)$/i', $value, $matches)) {
            $token = trim($matches[1]);
            break;
        }
    }
    
    // Parse JSON request body
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    // Fallback: try to get token from request body
    if (!$token && isset($data['wstoken'])) {
        $token = $data['wstoken'];
    }
    
    // Fallback: try to get token from GET/POST parameters
    if (!$token && isset($_REQUEST['wstoken'])) {
        $token = $_REQUEST['wstoken'];
    }
    
    if (!$token) {
        throw new Exception('Authorization token required. Provide token in Authorization header (Bearer token) or in request body as wstoken');
    }
    
    // Validate token and authenticate user
    global $DB, $USER;
    
    // Ensure we have database connection
    if (!$DB) {
        throw new Exception('Database connection failed');
    }
    
    $tokenrecord = $DB->get_record('external_tokens', array('token' => $token));
    
    if (!$tokenrecord) {
        throw new Exception('Invalid token');
    }
    
    if ($tokenrecord->validuntil > 0 && $tokenrecord->validuntil < time()) {
        throw new Exception('Token expired');
    }
    
    // Load authenticated user and set context
    $USER = $DB->get_record('user', array('id' => $tokenrecord->userid));
    if (!$USER) {
        throw new Exception('User not found');
    }
    
    // Get requested function
    $wsfunction = $data['wsfunction'] ?? null;
    
    // Default function if not specified
    if (!$wsfunction) {
        $wsfunction = 'local_coursecopy_copy_course';
    }
    
    // Route to appropriate function
    switch ($wsfunction) {
        case 'local_coursecopy_copy_course':
            $shortname_clone = $data['shortname_clone'] ?? '';
            $fullname = $data['fullname'] ?? '';
            $shortname = $data['shortname'] ?? '';
            $startdate = (int)($data['startdate'] ?? 0);
            $enddate = (int)($data['enddate'] ?? 0);
            
            if (!$shortname_clone || !$fullname || !$shortname || !$startdate || !$enddate) {
                throw new Exception('Missing required parameters: shortname_clone, fullname, shortname, startdate, enddate');
            }
            
            $result = local_coursecopy_external::copy_course($shortname_clone, $fullname, $shortname, $startdate, $enddate);
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
        'status' => 'error',
        'id' => 0,
        'message' => $e->getMessage()
    ), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}
