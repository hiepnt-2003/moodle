<?php
<?php
defined('MOODLE_INTERNAL') || die();

/**
 * Post installation procedure
 */
function xmldb_local_createtable_install() {
    global $DB;
    
    // Thêm dữ liệu mẫu vào bảng local_createtable_batches
    $sample_batches = [
        [
            'name' => 'Đợt mở môn Kỳ 1 - 2025',
            'open_date' => strtotime('2025-01-15'),
            'timecreated' => time()
        ],
        [
            'name' => 'Đợt mở môn Kỳ 2 - 2025', 
            'open_date' => strtotime('2025-06-15'),
            'timecreated' => time()
        ],
        [
            'name' => 'Đợt mở môn Hè - 2025',
            'open_date' => strtotime('2025-08-01'),
            'timecreated' => time()
        ]
    ];
    
    foreach ($sample_batches as $batch) {
        $DB->insert_record('local_createtable_batches', $batch);
    }
    
    return true;
}