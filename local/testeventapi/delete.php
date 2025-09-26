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
 * Delete batch page for Test Event API plugin.
 *
 * @package    local_testeventapi
 * @copyright  2025 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

// Check login and capabilities.
require_login();
require_capability('local/testeventapi:manage', context_system::instance());

$id = required_param('id', PARAM_INT); // Batch ID.
$confirm = optional_param('confirm', 0, PARAM_INT);

$PAGE->set_url(new moodle_url('/local/testeventapi/delete.php', ['id' => $id]));
$PAGE->set_context(context_system::instance());

// Get batch data.
$batch = \local_testeventapi\batch_manager::get_batch($id);
if (!$batch) {
    throw new moodle_exception('batchnotfound', 'local_testeventapi');
}

$PAGE->set_title(get_string('deletebatch', 'local_testeventapi'));
$PAGE->set_heading(get_string('deletebatch', 'local_testeventapi'));

// Handle confirmation.
if ($confirm && confirm_sesskey()) {
    try {
        $success = \local_testeventapi\batch_manager::delete_batch($id);
        if ($success) {
            redirect(new moodle_url('/local/testeventapi/index.php'), 
                get_string('batchdeleted', 'local_testeventapi'), null, \core\output\notification::NOTIFY_SUCCESS);
        } else {
            $errormsg = get_string('batchdeletefailed', 'local_testeventapi');
        }
    } catch (Exception $e) {
        $errormsg = $e->getMessage();
    }
}

echo $OUTPUT->header();

// Show error message if any.
if (isset($errormsg)) {
    echo $OUTPUT->notification($errormsg, 'error');
}

// Back button.
$backurl = new moodle_url('/local/testeventapi/view.php', ['id' => $id]);
echo html_writer::div(
    html_writer::link($backurl, get_string('back', 'local_testeventapi'), ['class' => 'btn btn-secondary']),
    'mb-3'
);

// Confirmation form.
echo $OUTPUT->confirm(
    get_string('confirmdeletebatch', 'local_testeventapi') . '<br><br>' .
    '<strong>' . format_string($batch->name) . '</strong><br>' .
    'Ngày bắt đầu: ' . userdate($batch->start_date, get_string('strftimedatefullshort')),
    new moodle_url('/local/testeventapi/delete.php', ['id' => $id, 'confirm' => 1, 'sesskey' => sesskey()]),
    new moodle_url('/local/testeventapi/view.php', ['id' => $id])
);

echo $OUTPUT->footer();