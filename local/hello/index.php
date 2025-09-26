<?php

require_once('../../config.php');

// Check if user is logged in
require_login();

// Set up the page
// Định nghĩa URL chính thức của trang
$PAGE->set_url('/local/hello/index.php');
// Thiết lập context hệ thống (system-wide)
$PAGE->set_context(context_system::instance());
// Tiêu đề hiển thị trên tab browser
$PAGE->set_title(get_string('pluginname', 'local_hello'));
// Tiêu đề chính trên trang
$PAGE->set_heading(get_string('pluginname', 'local_hello'));
// 	Sử dụng layout chuẩn của Moodle
$PAGE->set_pagelayout('standard');

// Start output HTML
echo $OUTPUT->header();

// Display content
echo html_writer::tag('h2', get_string('helloworld', 'local_hello'));
echo html_writer::tag('p', get_string('welcome', 'local_hello'));
echo html_writer::tag('p', get_string('message', 'local_hello'));

// Display current user info
echo html_writer::tag('p', 'Current user: ' . fullname($USER));
echo html_writer::tag('p', 'Current time: ' . date('Y-m-d H:i:s'));

// Add a simple form
echo html_writer::start_tag('div', array('style' => 'margin-top: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 5px;'));
echo html_writer::tag('h3', 'Simple Demo Form');
echo html_writer::start_tag('form', array('method' => 'get'));
echo html_writer::tag('label', 'Enter your name: ', array('for' => 'username'));
echo html_writer::empty_tag('input', array('type' => 'text', 'name' => 'username', 'id' => 'username', 'style' => 'margin: 0 10px;'));
echo html_writer::empty_tag('input', array('type' => 'submit', 'value' => 'Submit', 'class' => 'btn btn-primary'));
echo html_writer::end_tag('form');

// Display submitted name if available
if (isset($_GET['username']) && !empty($_GET['username'])) {
    $username = clean_param($_GET['username'], PARAM_TEXT);
    echo html_writer::tag('p', 'Hello, ' . $username . '!', array('style' => 'color: green; font-weight: bold;'));
}

echo html_writer::end_tag('div');

// Finish output
echo $OUTPUT->footer();