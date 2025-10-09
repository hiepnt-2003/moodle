<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

namespace theme_th_boost\output;

use html_writer;

defined('MOODLE_INTERNAL') || die;

/**
 * Core renderer for TH Boost theme.
 *
 * @package    theme_th_boost
 * @copyright  2025
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_renderer extends \theme_boost\output\core_renderer {

    /**
     * Override to add FontAwesome and password toggle functionality.
     *
     * @return string HTML to output.
     */
    public function standard_end_of_body_html() {
        // Lấy toàn bộ nội dung HTML/JS mặc định ở cuối trang từ theme Boost gốc
        $output = parent::standard_end_of_body_html();
        
        // Nối thêm thẻ <script> để tải file JavaScript của FontAwesome từ CDN.
        // $output .= html_writer::script('', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js');
        
        // Add password toggle script.
        $output .= html_writer::script('
            document.addEventListener("DOMContentLoaded", function() {
                // Tìm tất cả ô nhập mật khẩu
                const passwordFields = document.querySelectorAll(\'input[type="password"]\');
                
                // Lặp qua từng ô mật khẩu đã tìm thấy
                passwordFields.forEach(function(passwordField) {
                    // Skip if already wrapped
                    if (passwordField.parentElement.classList.contains("password-toggle-container")) {
                        return;
                    }
                    
                    // Tạo một thẻ <div> để bọc bên ngoài ô mật khẩu và nút bấm
                    const wrapper = document.createElement("div");
                    wrapper.className = "password-toggle-container";
                    
                    // Đặt input mật khẩu vào
                    passwordField.parentNode.insertBefore(wrapper, passwordField);
                    wrapper.appendChild(passwordField);
                    
                    // Tạo một nút <button>
                    const toggleBtn = document.createElement("button");
                    toggleBtn.type = "button";
                    toggleBtn.className = "password-toggle-btn";
                    toggleBtn.innerHTML = \'<i class="far fa-eye" aria-hidden="true"></i>\';
                    toggleBtn.setAttribute("aria-label", "Toggle password visibility");
                    toggleBtn.setAttribute("title", "Show password");
                    
                    // Thêm nút vào sau ô mật khẩu
                    wrapper.appendChild(toggleBtn);

                    // Thêm sự kiện click
                    toggleBtn.addEventListener("click", function() {

                        // Lấy ra loại (type) hiện tại của ô input, nếu là "password" thì đổi thành "text" và ngược lại
                        const type = passwordField.getAttribute("type") === "password" ? "text" : "password";
                        
                        // Cập nhật lại loại (type) cho ô input
                        passwordField.setAttribute("type", type);
                        
                        // Lấy ra thẻ <i> chứa icon
                        const icon = toggleBtn.querySelector("i");
                        if (type === "text") {
                            // Mắt gạch chéo  : password hiện
                            icon.className = "far fa-eye-slash";
                            toggleBtn.setAttribute("title", "Hide password");
                        } else {
                            // Mắt mở : password ẩn
                            icon.className = "far fa-eye";
                            toggleBtn.setAttribute("title", "Show password");
                        }
                    });
                });
            });
        ');
        
        return $output;
    }

    /**
     * Override to add FontAwesome support in head.
     *
     * @return string HTML fragment.
     */
    // public function standard_head_html() {
    //     // Lấy toàn bộ nội dung HTML mặc định của thẻ <head> từ theme Boost gốc
    //     $output = parent::standard_head_html();
        
    //     // Nối thêm một thẻ <link> để tải file CSS của FontAwesome từ CDN.
    //     $output .= html_writer::tag('link', '', [
    //         'rel' => 'stylesheet',
    //         'href' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
    //         'integrity' => 'sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==',
    //         'crossorigin' => 'anonymous',
    //         'referrerpolicy' => 'no-referrer'
    //     ]);
        
    //     return $output;
    // }
}
