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
 * Main index page for batch management.
 *
 * @package    local_createtable
 * @copyright  2025 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

// Require login and check capabilities.
require_login();
$context = context_system::instance();
require_capability('local/createtable:view', $context);

// Set up the page.
$PAGE->set_url('/local/createtable/index.php');
$PAGE->set_context($context);
$PAGE->set_title(get_string('pluginname', 'local_createtable'));
$PAGE->set_heading(get_string('pluginname', 'local_createtable'));

// Include CSS.
$PAGE->requires->css('/local/createtable/styles/styles.css');

echo $OUTPUT->header();

// Get template data and render using Mustache.
$templatedata = \local_createtable\output\renderer::get_batch_list_data();
echo $OUTPUT->render_from_template('local_createtable/batch_list', $templatedata);

echo $OUTPUT->footer();
