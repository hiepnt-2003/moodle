<?php
// This file is part of Moodle - http://moodle.org/
//
// Simple test script for local_usercreation web service.
// This file should be placed in the root directory of Moodle for testing.

require_once('config.php');
require_once($CFG->libdir . '/filelib.php');

// Configuration for testing.
$token = 'YOUR_WEB_SERVICE_TOKEN_HERE'; // Replace with your actual token.
$domainname = $CFG->wwwroot;

// Test data for user creation.
$testdata = [
    'wstoken' => $token,
    'wsfunction' => 'local_usercreation_create_user',
    'moodlewsrestformat' => 'json',
    'username' => 'testuser' . time(),
    'firstname' => 'Test',
    'lastname' => 'User',
    'email' => 'testuser' . time() . '@example.com',
    'createpassword' => 1, // Auto-generate password.
    'password' => '', // Empty since we're auto-generating.
];

// Create curl request.
$curl = new curl();
$serverurl = $domainname . '/webservice/rest/server.php';

echo "Testing User Creation Web Service\n";
echo "================================\n\n";
echo "Endpoint: " . $serverurl . "\n";
echo "Function: local_usercreation_create_user\n\n";

echo "Test Data:\n";
foreach ($testdata as $key => $value) {
    if ($key !== 'wstoken') {
        echo "- $key: $value\n";
    }
}
echo "\n";

// Make the request.
echo "Making request...\n";
$response = $curl->post($serverurl, $testdata);

echo "Response:\n";
echo $response . "\n\n";

// Parse JSON response.
$result = json_decode($response, true);
if ($result) {
    echo "Parsed Response:\n";
    echo "- Status: " . ($result['status'] ?? 'N/A') . "\n";
    echo "- User ID: " . ($result['id'] ?? 'N/A') . "\n";
    echo "- Message: " . ($result['message'] ?? 'N/A') . "\n";
} else {
    echo "Failed to parse JSON response\n";
}

echo "\n=== Test with manual password ===\n";

// Test with manual password.
$testdata2 = [
    'wstoken' => $token,
    'wsfunction' => 'local_usercreation_create_user',
    'moodlewsrestformat' => 'json',
    'username' => 'manualuser' . time(),
    'firstname' => 'Manual',
    'lastname' => 'User',
    'email' => 'manualuser' . time() . '@example.com',
    'createpassword' => 0, // Manual password.
    'password' => 'StrongPass123!',
];

echo "Test Data 2:\n";
foreach ($testdata2 as $key => $value) {
    if ($key !== 'wstoken') {
        echo "- $key: $value\n";
    }
}
echo "\n";

$response2 = $curl->post($serverurl, $testdata2);
echo "Response 2:\n";
echo $response2 . "\n\n";

$result2 = json_decode($response2, true);
if ($result2) {
    echo "Parsed Response 2:\n";
    echo "- Status: " . ($result2['status'] ?? 'N/A') . "\n";
    echo "- User ID: " . ($result2['id'] ?? 'N/A') . "\n";
    echo "- Message: " . ($result2['message'] ?? 'N/A') . "\n";
}