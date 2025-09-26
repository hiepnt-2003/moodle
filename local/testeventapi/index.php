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
 * Main index page for Test Event API plugin.
 *
 * @package    local_testeventapi
 * @copyright  2025 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

// Check login and capabilities.
require_login();
require_capability('local/testeventapi:view', context_system::instance());

$PAGE->set_url(new moodle_url('/local/testeventapi/index.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('pluginname', 'local_testeventapi'));
$PAGE->set_heading(get_string('pluginname', 'local_testeventapi'));

// Add CSS for better styling.
$PAGE->requires->css('/local/testeventapi/styles/styles.css');

echo $OUTPUT->header();

// Check if user can manage batches.
$canmanage = has_capability('local/testeventapi:manage', context_system::instance());

// Page heading with action buttons.
echo $OUTPUT->heading(get_string('batchlist', 'local_testeventapi'));

if ($canmanage) {
    // Add batch button.
    $addurl = new moodle_url('/local/testeventapi/manage.php');
    echo html_writer::div(
        $OUTPUT->single_button($addurl, get_string('addbatch', 'local_testeventapi'), 'get', ['class' => 'btn btn-primary']),
        'text-right mb-3'
    );
}

// Get all batches.
$batches = \local_testeventapi\batch_manager::get_all_batches();

if (empty($batches)) {
    echo $OUTPUT->notification(get_string('nobatches', 'local_testeventapi'), 'info');
} else {
    // Create table.
    $table = new html_table();
    $table->head = [
        get_string('no', 'local_testeventapi'),
        get_string('batchname', 'local_testeventapi'),
        get_string('startdate', 'local_testeventapi'),
        get_string('timecreated', 'local_testeventapi'),
        get_string('totalcourses', 'local_testeventapi'),
        get_string('coursesbyevent', 'local_testeventapi'),
        get_string('actions', 'local_testeventapi'),
    ];
    
    $table->attributes['class'] = 'table table-striped';
    
    $counter = 1;
    foreach ($batches as $batch) {
        // Get statistics for this batch.
        $stats = \local_testeventapi\batch_manager::get_batch_statistics($batch->id);
        
        $row = [];
        
        // STT.
        $row[] = $counter++;
        
        // Batch name.
        $row[] = format_string($batch->name);
        
        // Start date.
        $row[] = date('d/m/Y', $batch->start_date);
        
        // Created date.
        $row[] = date('d/m/Y H:i', $batch->timecreated);
        
        // Total courses.
        $row[] = $stats ? $stats->total_courses : '0';
        
        // Courses by event.
        $row[] = $stats ? $stats->courses_by_event : '0';
        
        // Actions.
        $actions = [];
        
        // View action.
        $viewurl = new moodle_url('/local/testeventapi/view.php', ['id' => $batch->id]);
        $actions[] = html_writer::link($viewurl, get_string('view', 'local_testeventapi'), 
            ['class' => 'btn btn-sm btn-outline-primary']);
        
        if ($canmanage) {
            // Edit action.
            $editurl = new moodle_url('/local/testeventapi/manage.php', ['id' => $batch->id]);
            $actions[] = html_writer::link($editurl, get_string('edit', 'local_testeventapi'), 
                ['class' => 'btn btn-sm btn-outline-secondary']);
            
            // Delete action.
            $deleteurl = new moodle_url('/local/testeventapi/delete.php', ['id' => $batch->id]);
            $actions[] = html_writer::link($deleteurl, get_string('delete', 'local_testeventapi'), 
                ['class' => 'btn btn-sm btn-outline-danger',
                 'onclick' => 'return confirm("' . get_string('confirmdeletebatch', 'local_testeventapi') . '")']);
        }
        
        $row[] = implode(' ', $actions);
        
        $table->data[] = $row;
    }
    
    echo html_writer::table($table);
}

// Information box about Event API.
echo $OUTPUT->box_start('generalbox info');
echo html_writer::tag('h4', get_string('eventapi', 'local_testeventapi'));
echo html_writer::tag('p', 'Plugin này sử dụng Event API của Moodle để tự động quản lý các đợt học. Khi tạo một đợt học mới, hệ thống sẽ tự động tìm và thêm các môn học có cùng ngày bắt đầu.');
echo $OUTPUT->box_end();

echo $OUTPUT->footer();