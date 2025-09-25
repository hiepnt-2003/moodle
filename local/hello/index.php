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
 * Main page for local_hello plugin
 *
 * @package    local_hello
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');

// Check if user is logged in
require_login();

// Set up the page
$PAGE->set_url('/local/hello/index.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('pluginname', 'local_hello'));
$PAGE->set_heading(get_string('pluginname', 'local_hello'));
$PAGE->set_pagelayout('standard');

// Start output
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