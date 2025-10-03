<?php
/**
 * Debug version - Course Clone RESTful API
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    require_once('../../config.php');
    echo "Config loaded successfully\n";
    
    require_once($CFG->dirroot . '/local/courseclone/externallib.php');
    echo "External lib loaded successfully\n";
    
    // Set headers
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }
    
    // Get input
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON: ' . json_last_error_msg());
    }
    
    echo json_encode(array(
        'status' => 'success',
        'message' => 'Debug - API is working',
        'received_data' => $data,
        'moodle_version' => $CFG->version ?? 'unknown',
        'plugin_exists' => class_exists('local_courseclone_external')
    ), JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array(
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ), JSON_PRETTY_PRINT);
} catch (Error $e) {
    http_response_code(500);
    echo json_encode(array(
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ), JSON_PRETTY_PRINT);
}
?>