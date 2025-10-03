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
 * Debug helper for Course Copier API testing.
 * 
 * Usage: Truy cập từ browser: your-moodle-url/local/coursecopier/debug_api.php
 *
 * @package    local_coursecopier
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->libdir . '/adminlib.php');

// Require admin login
require_login();
require_capability('moodle/site:config', context_system::instance());

$PAGE->set_url('/local/coursecopier/debug_api.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_title('Course Copier API Debug');
$PAGE->set_heading('Course Copier API Debug');

echo $OUTPUT->header();

echo '<h2>Course Copier API Debug Tool</h2>';

// Check if web services are enabled
$webservicesenabled = $CFG->enablewebservices ?? false;
echo '<h3>Web Services Status</h3>';
echo '<p>Web services enabled: ' . ($webservicesenabled ? '<span style="color: green;">YES</span>' : '<span style="color: red;">NO</span>') . '</p>';

if (!$webservicesenabled) {
    echo '<p style="color: red;">Web services are not enabled. Please enable them in Site Administration > Plugins > Web services > Overview</p>';
}

// Check if REST protocol is enabled
$restprotocolenabled = false;
if ($webservicesenabled) {
    $enabledprotocols = explode(',', $CFG->webserviceprotocols ?? '');
    $restprotocolenabled = in_array('rest', $enabledprotocols);
    echo '<p>REST protocol enabled: ' . ($restprotocolenabled ? '<span style="color: green;">YES</span>' : '<span style="color: red;">NO</span>') . '</p>';
}

// List available functions
echo '<h3>Available Functions</h3>';
try {
    $functions = $DB->get_records_sql("
        SELECT f.name, f.classname, f.methodname, f.capabilities 
        FROM {external_functions} f 
        WHERE f.name LIKE 'local_coursecopier_%'
        ORDER BY f.name
    ");
    
    if (empty($functions)) {
        echo '<p style="color: red;">No Course Copier functions found. Plugin may not be installed properly.</p>';
    } else {
        echo '<table border="1" cellpadding="5">';
        echo '<tr><th>Function Name</th><th>Class</th><th>Method</th><th>Required Capabilities</th></tr>';
        foreach ($functions as $function) {
            echo '<tr>';
            echo '<td>' . $function->name . '</td>';
            echo '<td>' . $function->classname . '</td>';
            echo '<td>' . $function->methodname . '</td>';
            echo '<td>' . $function->capabilities . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    }
} catch (Exception $e) {
    echo '<p style="color: red;">Error checking functions: ' . $e->getMessage() . '</p>';
}

// List available services
echo '<h3>Available Services</h3>';
try {
    $services = $DB->get_records_sql("
        SELECT s.name, s.shortname, s.enabled 
        FROM {external_services} s 
        WHERE s.shortname LIKE '%coursecopier%' OR s.name LIKE '%Course Copier%'
        ORDER BY s.name
    ");
    
    if (empty($services)) {
        echo '<p style="color: orange;">No Course Copier services found. You may need to create a service manually.</p>';
    } else {
        echo '<table border="1" cellpadding="5">';
        echo '<tr><th>Service Name</th><th>Short Name</th><th>Enabled</th></tr>';
        foreach ($services as $service) {
            echo '<tr>';
            echo '<td>' . $service->name . '</td>';
            echo '<td>' . $service->shortname . '</td>';
            echo '<td>' . ($service->enabled ? '<span style="color: green;">YES</span>' : '<span style="color: red;">NO</span>') . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    }
} catch (Exception $e) {
    echo '<p style="color: red;">Error checking services: ' . $e->getMessage() . '</p>';
}

// Test with sample data
echo '<h3>Sample Test Data</h3>';
echo '<p>Bạn có thể sử dụng dữ liệu mẫu này để test API:</p>';

// Get some sample courses
try {
    $samplecourses = $DB->get_records('course', ['visible' => 1], 'id ASC', 'id, fullname, shortname', 0, 3);
    if (!empty($samplecourses)) {
        echo '<h4>Sample Courses Available for Copying:</h4>';
        echo '<ul>';
        foreach ($samplecourses as $course) {
            if ($course->id > 1) { // Skip site course
                echo '<li><strong>' . $course->shortname . '</strong> - ' . $course->fullname . ' (ID: ' . $course->id . ')</li>';
            }
        }
        echo '</ul>';
    }
} catch (Exception $e) {
    echo '<p>Error getting sample courses: ' . $e->getMessage() . '</p>';
}

// Current timestamp examples
$now = time();
$start_example = $now;
$end_example = $now + (6 * 30 * 24 * 60 * 60); // 6 months later

echo '<h4>Sample Timestamp Values:</h4>';
echo '<ul>';
echo '<li><strong>Current timestamp:</strong> ' . $now . ' (' . date('Y-m-d H:i:s', $now) . ')</li>';
echo '<li><strong>Start date example:</strong> ' . $start_example . ' (' . date('Y-m-d H:i:s', $start_example) . ')</li>';
echo '<li><strong>End date example:</strong> ' . $end_example . ' (' . date('Y-m-d H:i:s', $end_example) . ')</li>';
echo '</ul>';

// Token info
echo '<h4>Web Service Token:</h4>';
echo '<p>Để sử dụng API, bạn cần tạo web service token:</p>';
echo '<ol>';
echo '<li>Vào Site Administration > Plugins > Web services > Manage tokens</li>';
echo '<li>Tạo token mới cho user có đủ quyền</li>';
echo '<li>Chọn service "Course Copier Service" hoặc "All services"</li>';
echo '<li>Copy token để sử dụng trong Postman</li>';
echo '</ol>';

// API endpoint info
echo '<h4>API Endpoint:</h4>';
echo '<p><strong>URL:</strong> ' . $CFG->wwwroot . '/webservice/rest/server.php</p>';
echo '<p><strong>Method:</strong> POST</p>';
echo '<p><strong>Content-Type:</strong> application/x-www-form-urlencoded</p>';

// Sample curl commands
echo '<h3>Sample cURL Commands</h3>';

echo '<h4>1. Get Available Courses:</h4>';
echo '<pre style="background: #f5f5f5; padding: 10px; overflow-x: auto;">';
echo 'curl -X POST "' . $CFG->wwwroot . '/webservice/rest/server.php" \\' . "\n";
echo '  -H "Content-Type: application/x-www-form-urlencoded" \\' . "\n";
echo '  -d "wstoken=YOUR_TOKEN_HERE" \\' . "\n";
echo '  -d "wsfunction=local_coursecopier_get_available_courses" \\' . "\n";
echo '  -d "moodlewsrestformat=json" \\' . "\n";
echo '  -d "categoryid=0"';
echo '</pre>';

echo '<h4>2. Copy Course:</h4>';
echo '<pre style="background: #f5f5f5; padding: 10px; overflow-x: auto;">';
echo 'curl -X POST "' . $CFG->wwwroot . '/webservice/rest/server.php" \\' . "\n";
echo '  -H "Content-Type: application/x-www-form-urlencoded" \\' . "\n";
echo '  -d "wstoken=YOUR_TOKEN_HERE" \\' . "\n";
echo '  -d "wsfunction=local_coursecopier_copy_course" \\' . "\n";
echo '  -d "moodlewsrestformat=json" \\' . "\n";
echo '  -d "shortname_clone=ORIGINAL_COURSE_SHORTNAME" \\' . "\n";
echo '  -d "fullname=New Course Full Name" \\' . "\n";
echo '  -d "shortname=NEWCOURSE2025" \\' . "\n";
echo '  -d "startdate=' . $start_example . '" \\' . "\n";
echo '  -d "enddate=' . $end_example . '"';
echo '</pre>';

// Troubleshooting section
echo '<h3>Troubleshooting</h3>';
echo '<ul>';
echo '<li><strong>Plugin not installed:</strong> Kiểm tra thư mục local/coursecopier có đầy đủ file không</li>';
echo '<li><strong>Functions not found:</strong> Vào Site Administration > Notifications để update database</li>';
echo '<li><strong>Permission denied:</strong> User cần có quyền moodle/course:create, moodle/backup:backupcourse, moodle/restore:restorecourse</li>';
echo '<li><strong>Invalid token:</strong> Kiểm tra token có đúng và còn hiệu lực không</li>';
echo '<li><strong>Service not enabled:</strong> Kích hoạt service trong Web services management</li>';
echo '</ul>';

echo $OUTPUT->footer();