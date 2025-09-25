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
    
    /**
     * Kiểm tra xem người dùng hiện tại có phải admin hay manager không
     * @return bool true nếu có quyền, false nếu không có quyền
     */
    private function is_admin_or_manager() {
        $context = context_system::instance();
        
        // Kiểm tra các quyền admin và manager
        if (has_capability('moodle/site:config', $context) ||               // Site Administrator
            has_capability('moodle/course:create', $context) ||              // Manager/Course Creator
            has_capability('moodle/user:create', $context) ||                // User Management
            has_capability('block/mydata:viewreports', $context)) {          // Custom permission
            return true;
        }
        
        return false;
    }

    public function get_content() {
        global $DB, $OUTPUT, $CFG;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();
        
        // Sử dụng hàm kiểm tra quyền
        if ($this->is_admin_or_manager()) {
            // Nếu có quyền admin hoặc manager, hiển thị link
            $view_url = new moodle_url('/blocks/mydata/view.php');
            $link_text = html_writer::link($view_url, 'tại đây', ['style' => 'color:#0066cc; text-decoration:underline; font-weight:bold;']);
            
            $content = html_writer::tag('p', 'Nhấn ' . $link_text . ' để xem danh sách môn học và người dùng.', 
                ['style' => 'text-align:center; padding:15px; font-size:14px; line-height:1.5;']);
        } else {
            // Nếu không có quyền, hiển thị thông báo
            $content = html_writer::tag('p', 'Bạn không có quyền truy cập danh sách này', 
                ['style' => 'text-align:center; padding:15px; font-size:14px; line-height:1.5; color:#d9534f; background-color:#f2dede; border:1px solid #ebccd1; border-radius:4px;']);
        }

        $this->content->text = $content;
        return $this->content;
    }
}
