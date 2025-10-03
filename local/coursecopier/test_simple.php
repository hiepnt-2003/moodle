<?php
// Simple test script for Course Copier plugin
require_once('../../config.php');
require_once('externallib.php');

// Must be logged in
require_login();

// Must be admin
require_capability('moodle/site:config', context_system::instance());

echo "<!DOCTYPE html>
<html>
<head>
    <title>Course Copier Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; }
        .error { color: red; }
        form { margin: 20px 0; }
        input[type='text'], input[type='number'] { padding: 5px; margin: 5px 0; }
        button { padding: 10px 15px; background: #0073aa; color: white; border: none; cursor: pointer; }
        .info { background: #f0f0f0; padding: 10px; margin: 10px 0; }
    </style>
</head>
<body>";

echo "<h1>Course Copier Plugin Test</h1>";

// Test get available courses
try {
    $courses = local_coursecopier_external::get_available_courses(0);
    echo "<div class='success'>✓ Get available courses: SUCCESS</div>";
    echo "<div class='info'>Found " . count($courses) . " courses</div>";
} catch (Exception $e) {
    echo "<div class='error'>✗ Get available courses: ERROR - " . $e->getMessage() . "</div>";
}

// Test form for course copying
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['copy_course'])) {
    $shortname_clone = $_POST['shortname_clone'];
    $fullname = $_POST['fullname'];
    $shortname = $_POST['shortname'];
    $startdate = strtotime($_POST['startdate']);
    $enddate = strtotime($_POST['enddate']);
    
    try {
        $result = local_coursecopier_external::copy_course(
            $shortname_clone, $fullname, $shortname, $startdate, $enddate
        );
        
        if ($result['status'] == 'success') {
            echo "<div class='success'>✓ Course copy: SUCCESS</div>";
            echo "<div class='info'>New course ID: " . $result['id'] . "</div>";
            echo "<div class='info'>Message: " . $result['message'] . "</div>";
        } else {
            echo "<div class='error'>✗ Course copy: ERROR</div>";
            echo "<div class='error'>Message: " . $result['message'] . "</div>";
        }
    } catch (Exception $e) {
        echo "<div class='error'>✗ Course copy: EXCEPTION - " . $e->getMessage() . "</div>";
    }
}

echo "<h2>Test Course Copy</h2>";
echo "<form method='POST'>
    <div>
        <label>Course to copy (shortname):</label><br>
        <input type='text' name='shortname_clone' value='test' required>
    </div>
    <div>
        <label>New course full name:</label><br>
        <input type='text' name='fullname' value='Test Course Copy' required>
    </div>
    <div>
        <label>New course short name:</label><br>
        <input type='text' name='shortname' value='testcopy" . time() . "' required>
    </div>
    <div>
        <label>Start date:</label><br>
        <input type='date' name='startdate' value='" . date('Y-m-d') . "' required>
    </div>
    <div>
        <label>End date:</label><br>
        <input type='date' name='enddate' value='" . date('Y-m-d', strtotime('+1 year')) . "' required>
    </div>
    <div>
        <button type='submit' name='copy_course'>Copy Course</button>
    </div>
</form>";

echo "<h2>Available Courses</h2>";
if (isset($courses)) {
    echo "<table border='1' cellpadding='5'>
            <tr>
                <th>ID</th>
                <th>Short Name</th>
                <th>Full Name</th>
            </tr>";
    foreach ($courses as $course) {
        echo "<tr>
                <td>{$course['id']}</td>
                <td>{$course['shortname']}</td>
                <td>{$course['fullname']}</td>
              </tr>";
    }
    echo "</table>";
}

echo "<p><a href='../../'>← Back to Moodle</a></p>";
echo "</body></html>";
?>