<?php
// Simple test file to check if plugin directory is accessible
require_once(__DIR__ . '/../../config.php');

echo "Plugin directory is accessible!<br>";
echo "Moodle version: " . $CFG->version . "<br>";
echo "Plugin path: " . __DIR__ . "<br>";

if (has_capability('report/useractivitylog:view', context_system::instance())) {
    echo "You have permission to view logs<br>";
} else {
    echo "You do NOT have permission to view logs<br>";
}

echo '<a href="index.php">Go to main plugin page</a>';
?>