<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

defined('MOODLE_INTERNAL') || die();

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
        
        // Kiểm tra quyền manager
        $context = context_system::instance();
        if (has_capability('block/mydata:viewreports', $context) || has_capability('moodle/site:config', $context)) {
            // Nếu là manager, hiển thị link
            $view_url = new moodle_url('/blocks/mydata/view.php');
            $link_text = html_writer::link($view_url, 'tại đây', ['style' => 'color:#0066cc; text-decoration:underline; font-weight:bold;']);
            
            $content = html_writer::tag('p', 'Nhấn ' . $link_text . ' để xem danh sách môn học và người dùng.', 
                ['style' => 'text-align:center; padding:15px; font-size:14px; line-height:1.5;']);
        } else {
            // Nếu không phải manager, hiển thị thông báo
            $content = html_writer::tag('p', 'Bạn không có quyền truy cập chức năng này. Chỉ có Manager mới có thể xem báo cáo.', 
                ['style' => 'text-align:center; padding:15px; font-size:14px; line-height:1.5; color:#d9534f; background-color:#f2dede; border:1px solid #ebccd1; border-radius:4px;']);
        }

        $this->content->text = $content;
        return $this->content;
    }
}
