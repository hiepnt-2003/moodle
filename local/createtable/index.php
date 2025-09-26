<?php
require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/local/createtable/lib.php');

// Require login and check capabilities
require_login();
$context = context_system::instance();
require_capability('moodle/site:config', $context);

// Set up the page
$PAGE->set_url('/local/createtable/index.php');
$PAGE->set_context($context);
$PAGE->set_title(get_string('pluginname', 'local_createtable'));
$PAGE->set_heading(get_string('pluginname', 'local_createtable'));

echo $OUTPUT->header();

echo html_writer::tag('h2', get_string('batchlist', 'local_createtable'));

// Lấy dữ liệu từ database
$batches = $DB->get_records('local_createtable_batches', null, 'open_date DESC');

if ($batches) {
    // Tạo bảng hiển thị
    $table = new html_table();
    $table->head = [
        get_string('batchname', 'local_createtable'),
        get_string('opendate', 'local_createtable'), 
        get_string('timecreated', 'local_createtable'),
        get_string('actions', 'local_createtable')
    ];
    $table->attributes['class'] = 'generaltable';
    
    foreach ($batches as $batch) {
        $row = [];
        $row[] = format_string($batch->name);
        $row[] = local_createtable_format_datetime($batch->open_date);
        $row[] = local_createtable_format_datetime($batch->timecreated);
        
        // Thêm các nút action
        $actions = '';
        $actions .= html_writer::link(
            new moodle_url('/local/createtable/manage.php', ['id' => $batch->id]),
            get_string('edit'),
            ['class' => 'btn btn-sm btn-secondary mr-1']
        );
        $actions .= html_writer::link(
            new moodle_url('/local/createtable/view.php', ['id' => $batch->id]),
            get_string('view'),
            ['class' => 'btn btn-sm btn-primary mr-1']
        );
        $row[] = $actions;
        
        $table->data[] = $row;
    }
    
    echo html_writer::table($table);
} else {
    echo $OUTPUT->notification(get_string('nobatches', 'local_createtable'), 'info');
}

// Thêm nút tạo mới
echo html_writer::div(
    html_writer::link(
        new moodle_url('/local/createtable/manage.php'),
        get_string('addbatch', 'local_createtable'),
        ['class' => 'btn btn-primary']
    ),
    'mt-3'
);

echo $OUTPUT->footer();
