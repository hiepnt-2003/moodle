<?php
/**
 * Library functions for block_mydata
 *
 * @package    block_mydata
 * @copyright  2025
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Kiểm tra xem người dùng hiện tại có quyền Admin hoặc Manager không
 * @return bool true nếu có quyền, false nếu không có quyền
 */
function block_mydata_has_access_permission() {
    $context = context_system::instance();
    
    // Kiểm tra quyền Site Administrator hoặc Manager
    if (has_capability('moodle/site:config', $context) ||               // Site Administrator
        has_capability('moodle/course:create', $context) ||              // Manager/Course Creator
        has_capability('block/mydata:viewreports', $context)) {          // Custom Manager permission
        return true;
    }
    
    return false;
}

/**
 * Lấy danh sách các khóa học hiển thị (visible = 1)
 * @return array Mảng các khóa học với key là id và value là tên hiển thị
 */
function block_mydata_get_visible_courses() {
    global $DB;
    
    $courses = $DB->get_records_sql("
        SELECT id, fullname, shortname 
        FROM {course} 
        WHERE id != 1 AND visible = 1
        ORDER BY fullname ASC
    ");
    
    $course_options = array();
    foreach ($courses as $course) {
        $course_options[$course->id] = $course->fullname . ' (' . $course->shortname . ')';
    }
    
    return $course_options;
}

/**
 * Lấy danh sách người dùng trong một khóa học cụ thể
 * @param int $courseid ID của khóa học
 * @return array Mảng người dùng với thông tin chi tiết
 */
function block_mydata_get_course_users($courseid) {
    global $DB;
    
    $sql = "SELECT DISTINCT u.id, u.username, u.firstname, u.lastname, u.email, r.shortname as role
            FROM {user} u
            JOIN {user_enrolments} ue ON u.id = ue.userid
            JOIN {enrol} e ON ue.enrolid = e.id
            JOIN {context} ctx ON ctx.instanceid = e.courseid AND ctx.contextlevel = 50
            JOIN {role_assignments} ra ON ra.userid = u.id AND ra.contextid = ctx.id
            JOIN {role} r ON ra.roleid = r.id
            WHERE e.courseid = ? AND u.deleted = 0
            ORDER BY u.lastname ASC, u.firstname ASC";
    
    return $DB->get_records_sql($sql, array($courseid));
}

/**
 * Chuyển đổi role shortname thành tên hiển thị tiếng Việt
 * @param string $role_shortname Tên ngắn của vai trò
 * @return string Tên hiển thị của vai trò
 */
function block_mydata_get_role_display_name($role_shortname) {
    switch ($role_shortname) {
        case 'student':
            return get_string('role_student', 'block_mydata');
        case 'teacher':
            return get_string('role_teacher', 'block_mydata');
        case 'editingteacher':
            return get_string('role_editingteacher', 'block_mydata');
        case 'manager':
            return get_string('role_manager', 'block_mydata');
        case 'coursecreator':
            return get_string('role_coursecreator', 'block_mydata');
        default:
            return ucfirst($role_shortname);
    }
}

/**
 * Tạo HTML table cho danh sách người dùng
 * @param array $users Mảng người dùng
 * @return html_table Object bảng HTML
 */
function block_mydata_create_users_table($users) {
    $table = new html_table();
    $table->head = array(
        get_string('stt', 'block_mydata'), 
        get_string('username', 'block_mydata'), 
        get_string('fullname', 'block_mydata'), 
        get_string('email', 'block_mydata'), 
        get_string('role', 'block_mydata')
    );
    $table->attributes['class'] = 'generaltable';
    $table->attributes['style'] = 'width: 100%; margin-bottom: 20px;';
    
    $stt = 1;
    foreach ($users as $user) {
        $fullname = fullname($user);
        
        // Tạo link đến profile người dùng
        $user_url = new moodle_url('/user/profile.php', array('id' => $user->id));
        $username_link = html_writer::link($user_url, $user->username, 
            array('style' => 'color: #0066cc; text-decoration: none;'));
        $fullname_link = html_writer::link($user_url, $fullname, 
            array('style' => 'color: #0066cc; text-decoration: none;'));
        
        // Định dạng vai trò
        $role_display = block_mydata_get_role_display_name($user->role);
        
        $table->data[] = array(
            $stt,
            $username_link,
            $fullname_link,
            $user->email,
            $role_display
        );
        $stt++;
    }
    
    return $table;
}