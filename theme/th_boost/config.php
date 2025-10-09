<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

defined('MOODLE_INTERNAL') || die();

$THEME->name = 'th_boost';
$THEME->sheets = [];
$THEME->editor_sheets = [];
$THEME->parents = ['boost'];
$THEME->enable_dock = false;
$THEME->yuicssmodules = [];
$THEME->rendererfactory = 'theme_overridden_renderer_factory';
$THEME->requiredblocks = '';
$THEME->addblockposition = BLOCK_ADDBLOCK_POSITION_FLATNAV;

// Lấy các cài đặt màu sắc (brandcolor) và các đoạn mã trong ô "SCSS ban đầu" từ cơ sở dữ liệu.
$THEME->prescsscallback = 'theme_th_boost_get_pre_scss';

// Lấy nội dung của file "preset" (ví dụ: default.scss từ theme Boost gốc).
// -->Trả về một chuỗi SCSS khổng lồ chứa toàn bộ giao diện mặc định của Moodle
$THEME->scss = function($theme) {
    return theme_th_boost_get_main_scss_content($theme);
};

// Đọc file th_boost.scss (chứa CSS cho nút ẩn/hiện mật khẩu) và lấy mã từ ô "SCSS cuối cùng" của quản trị viên.
// -->Trả về một chuỗi SCSS chứa các quy tắc định dạng tùy chỉnh.
$THEME->extrascsscallback = 'theme_th_boost_get_extra_scss';

// Nếu cần một file CSS đã được biên dịch sẵn --> Dùng nó
$THEME->precompiledcsscallback = 'theme_th_boost_get_precompiled_css';
// Cho phép sử dụng cơ chế dự phòng
$THEME->usefallback = true;

// Khai báo một thuộc tính tùy chỉnh
$THEME->haseditswitch = true;
