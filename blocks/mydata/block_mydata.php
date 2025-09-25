<?php

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/blocks/mydata/lib.php');

class block_mydata extends block_base {
    public function init() {
        $this->title = get_string('pluginname', 'block_mydata');
    }

    public function get_content() {
        global $DB, $OUTPUT, $CFG;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();
        
        // Sử dụng hàm kiểm tra quyền từ lib.php
        if (block_mydata_has_access_permission()) {
            // Nếu có quyền admin hoặc manager, hiển thị các link
            $view_url = new moodle_url('/blocks/mydata/view.php');
            $report_url = new moodle_url('/blocks/mydata/report.php');
            
            $view_link = html_writer::link($view_url, get_string('view_all', 'block_mydata'), 
                ['style' => 'color:#0066cc; text-decoration:underline; font-weight:bold; margin-right:10px;']);
            $report_link = html_writer::link($report_url, get_string('course_report', 'block_mydata'), 
                ['style' => 'color:#0066cc; text-decoration:underline; font-weight:bold;']);
            
            $content = html_writer::tag('div', 
                html_writer::tag('p', '📊 ' . $view_link . ' | ' . $report_link, 
                    ['style' => 'text-align:center; margin:10px 0;']) .
                html_writer::tag('p', get_string('description_navigation', 'block_mydata'), 
                    ['style' => 'text-align:center; font-size:12px; color:#666; margin:0;']),
                ['style' => 'padding:15px; border:2px solid #0066cc; border-radius:8px; background-color:#f8f9ff;']
            );
        } else {
            // Nếu không có quyền, hiển thị thông báo
            $content = html_writer::tag('p', '🔒 ' . get_string('no_permission', 'block_mydata'), 
                ['style' => 'text-align:center; padding:15px; font-size:14px; line-height:1.5; color:#d9534f; background-color:#f2dede; border:1px solid #ebccd1; border-radius:4px;']);
        }

        $this->content->text = $content;
        return $this->content;
    }
}
