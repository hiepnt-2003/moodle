<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

defined('MOODLE_INTERNAL') || die();

/**
 * Hàm này có nhiệm vụ lấy nội dung SCSS chính, hay còn gọi là "preset" (bộ cài đặt giao diện có sẵn).
 * Nó làm gì? Nó kiểm tra xem quản trị viên đã chọn preset nào trong phần cài đặt theme.
 * Nếu là default.scss hoặc plain.scss, nó sẽ lấy file tương ứng từ theme Boost gốc của Moodle.
 * Nếu là một file khác (ví dụ my_preset.scss), nó sẽ tìm file mà quản trị viên đã tải lên.
 * Nếu không có lựa chọn nào, nó sẽ mặc định dùng file default.scss của theme Boost.
 * Mục đích: Xác định bộ giao diện nền tảng cho toàn bộ trang web.
 */
function theme_th_boost_get_main_scss_content($theme) {
    global $CFG;

    $scss = '';
    $filename = !empty($theme->settings->preset) ? $theme->settings->preset : null;
    $fs = get_file_storage();

    $context = context_system::instance();
    if ($filename == 'default.scss') {
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
    } else if ($filename == 'plain.scss') {
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/plain.scss');
    } else if ($filename && ($presetfile = $fs->get_file($context->id, 'theme_th_boost', 'preset', 0, '/', $filename))) {
        $scss .= $presetfile->get_content();
    } else {
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
    }

    return $scss;
}

/**
 * Hàm này lấy nội dung SCSS để chèn vào TRƯỚC file SCSS chính. "Pre" có nghĩa là "trước".
 * Nó kiểm tra các cài đặt trong theme, ví dụ như brandcolor (màu thương hiệu). 
 * Nếu quản trị viên đã chọn một màu trong giao diện cài đặt (ví dụ: #ff0000), 
 * hàm này sẽ tạo ra một dòng mã SCSS: $brandcolor: #ff0000;.
 * Nó kiểm tra xem quản trị viên có nhập mã tùy chỉnh vào ô "SCSS ban đầu" (scsspre) trong cài đặt không và chèn mã đó vào.
 * Mục đích: Định nghĩa các biến (variables) SCSS. 
 * Bằng cách chèn các biến này vào trước, chúng ta có thể ghi đè các giá trị mặc định trong file preset. 
 * Đây chính là cách bạn thay đổi màu sắc của toàn bộ trang web chỉ bằng một lựa chọn trong cài đặt. 
*/
function theme_th_boost_get_pre_scss($theme) {
    global $CFG;

    $scss = '';
    $configurable = [
        'brandcolor' => ['brandcolor'],
    ];

    // Prepend variables first.
    foreach ($configurable as $configkey => $targets) {
        $value = isset($theme->settings->{$configkey}) ? $theme->settings->{$configkey} : null;
        if (empty($value)) {
            continue;
        }
        array_map(function($target) use (&$scss, $value) {
            $scss .= '$' . $target . ': ' . $value . ";\n";
        }, (array) $targets);
    }

    // Prepend pre-scss.
    if (!empty($theme->settings->scsspre)) {
        $scss .= $theme->settings->scsspre;
    }

    return $scss;
}

/**
 *  Mục đích: Thêm các định dạng tùy chỉnh và ghi đè lên các định dạng của preset. 
 *  Vì được chèn vào cuối cùng nên các quy tắc CSS ở đây có độ ưu tiên cao nhất.
 */
function theme_th_boost_get_extra_scss($theme) {
    global $CFG;
    
    $content = '';
    
    // Load custom SCSS for password toggle.
    $content .= file_get_contents($CFG->dirroot . '/theme/th_boost/scss/th_boost.scss');
    
    if (!empty($theme->settings->scss)) {
        $content .= $theme->settings->scss;
    }
    
    return $content;
}

/**
 * Mục đích: Trong trường hợp máy chủ không thể biên dịch SCSS (do cấu hình hoặc lỗi), 
 * Moodle sẽ sử dụng file CSS này để trang web không bị mất hoàn toàn giao diện.
 */
function theme_th_boost_get_precompiled_css() {
    global $CFG;
    return file_get_contents($CFG->dirroot . '/theme/th_boost/style/moodle.css');
}

/**
 * Đây là một hàm chuẩn của Moodle, có nhiệm vụ phục vụ (serve) các file mà quản trị viên đã tải lên thông qua trang cài đặt theme.
 * Khi trình duyệt yêu cầu một file (ví dụ: logo của trang web), 
 * Moodle sẽ gọi hàm này. Hàm sẽ kiểm tra khu vực file (filearea) là logo, backgroundimage (ảnh nền), hay preset 
 * để trả về đúng file một cách an toàn.

 * Mục đích: Đảm bảo các file media của theme (logo, ảnh nền, v.v.) được hiển thị một cách bảo mật.
 */
function theme_th_boost_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    if ($context->contextlevel == CONTEXT_SYSTEM) {
        $theme = theme_config::load('th_boost');
        if ($filearea === 'logo') {
            return $theme->setting_file_serve('logo', $args, $forcedownload, $options);
        } else if ($filearea === 'backgroundimage') {
            return $theme->setting_file_serve('backgroundimage', $args, $forcedownload, $options);
        } else if (preg_match("/^loginbackgroundimage[1-9][0-9]?$/", $filearea) !== false) {
            return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
        } else if ($filearea === 'preset') {
            return $theme->setting_file_serve('preset', $args, $forcedownload, $options);
        } else {
            send_file_not_found();
        }
    } else {
        send_file_not_found();
    }
}

/**
 * Một hàm bổ sung để quản lý việc nhúng FontAwesome.
 * Nó làm gì? Nó trả về một dòng mã SCSS duy nhất: @import "fontawesome";.
 * Mục đích: Đây là một cách để Moodle biết rằng cần phải nhúng thư viện FontAwesome vào quá trình biên dịch SCSS.
 */
function theme_th_boost_get_extra_scss_fontawesome($theme) {
    $scss = '';
    
    // Add FontAwesome.
    $scss .= '@import "fontawesome";';
    
    return $scss;
}
