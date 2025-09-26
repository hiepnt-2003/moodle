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
 * View batch details page for Test Event API plugin.
 *
 * @package    local_testeventapi
 * @copyright  2025 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

// Check login and capabilities.
require_login();
require_capability('local/testeventapi:view', context_system::instance());

$id = required_param('id', PARAM_INT); // Batch ID.

$PAGE->set_url(new moodle_url('/local/testeventapi/view.php', ['id' => $id]));
$PAGE->set_context(context_system::instance());

// Get batch data.
$batch = \local_testeventapi\batch_manager::get_batch($id);
if (!$batch) {
    throw new moodle_exception('batchnotfound', 'local_testeventapi');
}

$PAGE->set_title(get_string('batchdetail', 'local_testeventapi') . ': ' . format_string($batch->name));
$PAGE->set_heading(get_string('batchdetail', 'local_testeventapi'));

// Check if user can manage batches.
$canmanage = has_capability('local/testeventapi:manage', context_system::instance());

echo $OUTPUT->header();

// Back button.
$backurl = new moodle_url('/local/testeventapi/index.php');
echo html_writer::div(
    html_writer::link($backurl, get_string('back', 'local_testeventapi'), ['class' => 'btn btn-secondary']),
    'mb-3'
);

// Batch information.
echo $OUTPUT->heading(get_string('batchinfo', 'local_testeventapi'), 3);

$infotable = new html_table();
$infotable->attributes['class'] = 'table table-bordered';
$infotable->data = [
    [get_string('batchname', 'local_testeventapi'), format_string($batch->name)],
    [get_string('startdate', 'local_testeventapi'), date('d/m/Y', $batch->start_date)],
    [get_string('timecreated', 'local_testeventapi'), date('d/m/Y H:i', $batch->timecreated)],
];

echo html_writer::table($infotable);

// Action buttons for managers.
if ($canmanage) {
    $editurl = new moodle_url('/local/testeventapi/manage.php', ['id' => $id]);
    $deleteurl = new moodle_url('/local/testeventapi/delete.php', ['id' => $id]);
    
    echo html_writer::div(
        html_writer::link($editurl, get_string('edit', 'local_testeventapi'), ['class' => 'btn btn-primary']) . ' ' .
        html_writer::link($deleteurl, get_string('delete', 'local_testeventapi'), 
            ['class' => 'btn btn-danger', 
             'onclick' => 'return confirm("' . get_string('confirmdeletebatch', 'local_testeventapi') . '")']),
        'mb-3'
    );
}

// Statistics.
$stats = \local_testeventapi\batch_manager::get_batch_statistics($id);
if ($stats) {
    echo $OUTPUT->heading(get_string('statistics', 'local_testeventapi'), 3);
    
    $statstable = new html_table();
    $statstable->attributes['class'] = 'table table-bordered';
    $statstable->data = [
        [get_string('totalcourses', 'local_testeventapi'), $stats->total_courses],
        [get_string('coursesbyevent', 'local_testeventapi'), 
         $stats->courses_by_event . ' <span class="badge badge-success">Event API</span>'],
        [get_string('coursesmanual', 'local_testeventapi'), 
         $stats->courses_manual . ' <span class="badge badge-info">Manual</span>'],
        [get_string('availablecourses', 'local_testeventapi'), $stats->available_courses],
    ];
    
    echo html_writer::table($statstable);
}

// Courses list.
echo $OUTPUT->heading(get_string('courseslist', 'local_testeventapi'), 3);

$courses = \local_testeventapi\batch_manager::get_batch_courses($id);

if (empty($courses)) {
    echo $OUTPUT->notification(get_string('nocourses', 'local_testeventapi'), 'info');
} else {
    $coursestable = new html_table();
    $coursestable->head = [
        get_string('no', 'local_testeventapi'),
        get_string('coursename', 'local_testeventapi'),
        get_string('shortname', 'local_testeventapi'),
        get_string('startdate', 'local_testeventapi'),
        get_string('dateadded', 'local_testeventapi'),
        get_string('addmethod', 'local_testeventapi'),
    ];
    
    $coursestable->attributes['class'] = 'table table-striped';
    
    $counter = 1;
    foreach ($courses as $course) {
        $row = [];
        
        // STT.
        $row[] = $counter++;
        
        // Course name (link to course).
        $courseurl = new moodle_url('/course/view.php', ['id' => $course->id]);
        $row[] = html_writer::link($courseurl, format_string($course->fullname));
        
        // Short name.
        $row[] = format_string($course->shortname);
        
        // Course start date.
        $row[] = date('d/m/Y', $course->startdate);
        
        // Date added to batch.
        $row[] = date('d/m/Y H:i', $course->added_time);
        
        // Add method.
        if ($course->added_by_event) {
            $row[] = '<span class="badge badge-success">' . get_string('addedbyevent', 'local_testeventapi') . '</span>';
        } else {
            $row[] = '<span class="badge badge-info">' . get_string('addedmanually', 'local_testeventapi') . '</span>';
        }
        
        $coursestable->data[] = $row;
    }
    
    echo html_writer::table($coursestable);
}

// Information box about automatic course addition.
echo $OUTPUT->box_start('generalbox info');
echo html_writer::tag('h5', 'Về Event API');
echo html_writer::tag('p', 'Các môn học có <span class="badge badge-success">Event API</span> được thêm tự động khi:');
echo html_writer::start_tag('ul');
echo html_writer::tag('li', 'Đợt học được tạo mới (event: batch_created)');
echo html_writer::tag('li', 'Đợt học được cập nhật với ngày bắt đầu mới (event: batch_updated)');
echo html_writer::end_tag('ul');
echo html_writer::tag('p', 'Hệ thống sẽ tự động tìm tất cả môn học có ngày bắt đầu trùng với ngày bắt đầu của đợt học và thêm vào danh sách.');
echo $OUTPUT->box_end();

echo $OUTPUT->footer();