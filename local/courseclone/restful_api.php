<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * RESTful API Middleware for Bearer Token Support
 * Compatible with Moodle 3.10 + RESTful Protocol Plugin
 * 
 * @package    local_courseclone
 * @copyright  2024 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Check if RESTful protocol plugin is available
if (!class_exists('webservice_restful_server')) {
    // Fallback for environments without RESTful plugin
    define('RESTFUL_PLUGIN_AVAILABLE', false);
} else {
    define('RESTFUL_PLUGIN_AVAILABLE', true);
}

/**
 * RESTful API Handler Class
 */
class local_courseclone_restful_api {

    /**
     * Process RESTful API request with Bearer Token support
     * Enhanced for Moodle 3.10 + RESTful Protocol Plugin
     * 
     * @return array API response
     */
    public static function process_request() {
        global $_SERVER, $_POST;
        
        // Enhanced header detection for Moodle 3.10
        $headers = self::get_request_headers();
        $authorization = null;
        
        // Get Authorization header (case-insensitive)
        foreach ($headers as $key => $value) {
            if (strtolower($key) === 'authorization') {
                $authorization = $value;
                break;
            }
        }
        
        // Extract Bearer token
        if ($authorization && preg_match('/Bearer\s+(.*)$/i', $authorization, $matches)) {
            $token = trim($matches[1]);
            
            // Enhanced JSON processing for RESTful plugin compatibility
            $contentType = $_SERVER['CONTENT_TYPE'] ?? $_SERVER['HTTP_CONTENT_TYPE'] ?? '';
            if (strpos($contentType, 'application/json') !== false) {
                $json = file_get_contents('php://input');
                $data = json_decode($json, true);
                
                if ($data && is_array($data)) {
                    // Add token to data and merge with existing $_POST
                    $data['wstoken'] = $token;
                    
                    // For RESTful plugin compatibility, ensure proper parameter mapping
                    if (RESTFUL_PLUGIN_AVAILABLE) {
                        $_POST = $data; // Direct assignment for RESTful plugin
                    } else {
                        $_POST = array_merge($_POST, $data); // Fallback merge
                    }
                    
                    // Set function and format if not provided
                    if (!isset($_POST['wsfunction']) && isset($data['wsfunction'])) {
                        $_POST['wsfunction'] = $data['wsfunction'];
                    }
                    if (!isset($_POST['moodlewsrestformat'])) {
                        $_POST['moodlewsrestformat'] = 'json';
                    }
                    
                    // Store original request for debugging
                    $_POST['_original_json'] = $json;
                }
            }
            
            return array(
                'status' => 'success',
                'message' => 'Bearer token processed (RESTful plugin: ' . (RESTFUL_PLUGIN_AVAILABLE ? 'enabled' : 'disabled') . ')',
                'token_found' => true,
                'function' => $_POST['wsfunction'] ?? null,
                'restful_plugin' => RESTFUL_PLUGIN_AVAILABLE
            );
        }
        
        return array(
            'status' => 'no_bearer_token',
            'message' => 'No Bearer token found, using standard processing',
            'token_found' => false,
            'restful_plugin' => RESTFUL_PLUGIN_AVAILABLE
        );
    }
    
    /**
     * Get request headers in a compatible way for Moodle 3.10
     * 
     * @return array Headers array
     */
    private static function get_request_headers() {
        if (function_exists('getallheaders')) {
            return getallheaders();
        }
        
        // Fallback for environments where getallheaders() is not available
        $headers = array();
        foreach ($_SERVER as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
                $headers[$header] = $value;
            }
        }
        return $headers;
    }
    
    /**
     * Validate Bearer Token
     * 
     * @param string $token Token to validate
     * @return bool True if valid
     */
    public static function validate_bearer_token($token) {
        global $DB;
        
        try {
            // Check if token exists in external_tokens table
            $tokenrecord = $DB->get_record('external_tokens', array('token' => $token));
            
            if ($tokenrecord) {
                // Check if token is valid and not expired
                if ($tokenrecord->validuntil == 0 || $tokenrecord->validuntil > time()) {
                    return true;
                }
            }
            
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Get user from Bearer Token
     * 
     * @param string $token Bearer token
     * @return object|false User object or false
     */
    public static function get_user_from_token($token) {
        global $DB;
        
        try {
            $sql = "SELECT u.* 
                    FROM {external_tokens} et
                    JOIN {user} u ON u.id = et.userid  
                    WHERE et.token = ? 
                    AND (et.validuntil = 0 OR et.validuntil > ?)";
            
            $user = $DB->get_record_sql($sql, array($token, time()));
            return $user;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Send JSON Response
     * 
     * @param array $data Response data
     * @param int $status HTTP status code
     */
    public static function send_json_response($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
    
    /**
     * Handle CORS Preflight Request
     */
    public static function handle_cors_preflight() {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
            header('Access-Control-Allow-Headers: Content-Type, Authorization');
            header('Access-Control-Max-Age: 86400'); // 24 hours
            http_response_code(200);
            exit;
        }
    }
}

/**
 * RESTful Endpoint Handler
 * 
 * This function should be called at the beginning of webservice/rest/server.php
 * to handle RESTful API requests with Bearer tokens
 */
function local_courseclone_handle_restful_request() {
    // Handle CORS preflight
    local_courseclone_restful_api::handle_cors_preflight();
    
    // Process Bearer token if present
    $result = local_courseclone_restful_api::process_request();
    
    // Log the processing result for debugging
    if (defined('MOODLE_INTERNAL')) {
        error_log('RESTful API Processing: ' . json_encode($result));
    }
    
    return $result;
}