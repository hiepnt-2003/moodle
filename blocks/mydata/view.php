<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

require_once('../../config.php');

defined('MOODLE_INTERNAL') || die();

// Require login
require_login();

// Set up the page
$PAGE->set_url('/blocks/mydata/view.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_title('Danh sách khóa học và người dùng');
$PAGE->set_heading('Danh sách khóa học và người dùng');
$PAGE->set_pagelayout('standard');

echo $OUTPUT->header();

// 🔹 Lấy danh sách courses với thông tin category
$sql = "SELECT c.id, c.fullname, c.shortname, c.startdate, c.enddate, cc.name as categoryname
        FROM {course} c
        LEFT JOIN {course_categories} cc ON c.category = cc.id
        WHERE c.id != 1
        ORDER BY c.fullname ASC";
$courses = $DB->get_records_sql($sql);

$content = '';
$content .= html_writer::tag('h3', 'Danh sách khóa học');
$content .= html_writer::start_tag('table', ['class' => 'generaltable', 'style' => 'width:100%; margin-bottom:30px;']);
$content .= html_writer::start_tag('tr');
$headers = ['STT', 'Tên khóa học', 'Tên viết tắt', 'Ngày bắt đầu', 'Ngày kết thúc', 'Danh mục'];
foreach ($headers as $h) {
    $content .= html_writer::tag('th', $h, ['style' => 'border:1px solid #ddd; padding:10px; background-color:#f8f9fa; text-align:center;']);
}
$content .= html_writer::end_tag('tr');

$stt = 1;
foreach ($courses as $c) {
    $content .= html_writer::start_tag('tr', ['style' => 'border:1px solid #ddd;']);
    
    // Số thứ tự
    $content .= html_writer::tag('td', $stt, ['style' => 'border:1px solid #ddd; padding:10px; text-align:center;']);
    
    // Fullname với link đến course
    $course_url = new moodle_url('/course/view.php', array('id' => $c->id));
    $fullname_link = html_writer::link($course_url, $c->fullname, ['style' => 'color:#0066cc; text-decoration:none;']);
    $content .= html_writer::tag('td', $fullname_link, ['style' => 'border:1px solid #ddd; padding:10px;']);
    
    // Shortname với link đến course
    $shortname_link = html_writer::link($course_url, $c->shortname, ['style' => 'color:#0066cc; text-decoration:none;']);
    $content .= html_writer::tag('td', $shortname_link, ['style' => 'border:1px solid #ddd; padding:10px;']);
    
    // Start date (dd/mm/yyyy)
    $startdate = $c->startdate > 0 ? date('d/m/Y', $c->startdate) : 'Chưa xác định';
    $content .= html_writer::tag('td', $startdate, ['style' => 'border:1px solid #ddd; padding:10px; text-align:center;']);
    
    // End date (dd/mm/yyyy)
    $enddate = $c->enddate > 0 ? date('d/m/Y', $c->enddate) : 'Chưa xác định';
    $content .= html_writer::tag('td', $enddate, ['style' => 'border:1px solid #ddd; padding:10px; text-align:center;']);
    
    // Category name
    $categoryname = $c->categoryname ? $c->categoryname : 'Không có danh mục';
    $content .= html_writer::tag('td', $categoryname, ['style' => 'border:1px solid #ddd; padding:10px;']);
    
    $content .= html_writer::end_tag('tr');
    $stt++;
}
$content .= html_writer::end_tag('table');

// 🔹 Lấy danh sách users
$users = $DB->get_records('user', array('deleted' => 0), 'lastname ASC',
    'id, username, firstname, lastname, email');

$content .= html_writer::tag('h3', 'Danh sách người dùng');
$content .= html_writer::start_tag('table', ['class' => 'generaltable', 'style' => 'width:100%;']);
$content .= html_writer::start_tag('tr');
$headers = ['STT', 'Tên đăng nhập', 'Họ và tên', 'Email'];
foreach ($headers as $h) {
    $content .= html_writer::tag('th', $h, ['style' => 'border:1px solid #ddd; padding:10px; background-color:#f8f9fa; text-align:center;']);
}
$content .= html_writer::end_tag('tr');

$stt = 1;
foreach ($users as $u) {
    // Bỏ user guest và admin (id = 1,2)
    if ($u->id <= 2) continue;

    $fullname = fullname($u);
    $content .= html_writer::start_tag('tr', ['style' => 'border:1px solid #ddd;']);
    
    // Số thứ tự
    $content .= html_writer::tag('td', $stt, ['style' => 'border:1px solid #ddd; padding:10px; text-align:center;']);
    
    // Username với link đến user profile
    $user_url = new moodle_url('/user/profile.php', array('id' => $u->id));
    $username_link = html_writer::link($user_url, $u->username, ['style' => 'color:#0066cc; text-decoration:none;']);
    $content .= html_writer::tag('td', $username_link, ['style' => 'border:1px solid #ddd; padding:10px;']);
    
    // Fullname với link đến user profile
    $fullname_link = html_writer::link($user_url, $fullname, ['style' => 'color:#0066cc; text-decoration:none;']);
    $content .= html_writer::tag('td', $fullname_link, ['style' => 'border:1px solid #ddd; padding:10px;']);
    
    // Email
    $content .= html_writer::tag('td', $u->email, ['style' => 'border:1px solid #ddd; padding:10px;']);
    
    $content .= html_writer::end_tag('tr');
    $stt++;
}
$content .= html_writer::end_tag('table');

echo $content;

echo $OUTPUT->footer();