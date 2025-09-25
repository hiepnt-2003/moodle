<?php
require_once('../../config.php');
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/blocks/mydata/lib.php');

defined('MOODLE_INTERNAL') || die();

// Require login
require_login();

// Set up the page
$PAGE->set_url('/blocks/mydata/report.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('report_title', 'block_mydata'));
$PAGE->set_heading(get_string('report_heading', 'block_mydata'));
$PAGE->set_pagelayout('standard');

// Kiểm tra quyền Admin hoặc Manager
if (!block_mydata_has_access_permission()) {
    print_error('nopermissions', 'error', '', get_string('no_permission_error', 'block_mydata'));
}

/**
 * Form class để chọn khóa học
 */
class course_selection_form extends moodleform {
    
    public function definition() {
        $mform = $this->_form;
        
        // Lấy danh sách các khóa học hiển thị từ lib.php
        $course_options = block_mydata_get_visible_courses();
        
        // Thêm element autocomplete cho việc chọn nhiều khóa học
        $mform->addElement('autocomplete', 'courseids', get_string('select_courses', 'block_mydata'), $course_options, array(
            'multiple' => true,
            'placeholder' => get_string('course_placeholder', 'block_mydata'),
            'casesensitive' => false,
            'showsuggestions' => true
        ));
        $mform->addRule('courseids', get_string('select_course_required', 'block_mydata'), 'required', null, 'client');
        $mform->addHelpButton('courseids', 'courseselection', 'block_mydata');
        
        // Submit button
        $this->add_action_buttons(false, get_string('view_report', 'block_mydata'));
    }
}

echo $OUTPUT->header();

// Tạo và hiển thị form
$mform = new course_selection_form();

// Xử lý khi form được submit
if ($fromform = $mform->get_data()) {
    if (!empty($fromform->courseids)) {
        echo html_writer::tag('h3', get_string('selected_courses_report', 'block_mydata'));
        
        // Lấy thông tin các khóa học đã chọn
        list($insql, $params) = $DB->get_in_or_equal($fromform->courseids);
        $selected_courses = $DB->get_records_sql("
            SELECT id, fullname, shortname 
            FROM {course} 
            WHERE id $insql 
            ORDER BY fullname ASC
        ", $params);
        
        foreach ($selected_courses as $course) {
            $course_info = get_string('course_info', 'block_mydata', $course);
            echo html_writer::tag('h4', $course_info, 
                array('style' => 'margin-top: 30px; color: #0066cc;'));
            
            // Lấy danh sách người dùng từ lib.php
            $users = block_mydata_get_course_users($course->id);
            
            if (empty($users)) {
                echo html_writer::tag('p', get_string('no_users_in_course', 'block_mydata'), 
                    array('style' => 'font-style: italic; color: #666;'));
                continue;
            }
            
            // Tạo bảng từ lib.php
            $table = block_mydata_create_users_table($users);
            echo html_writer::table($table);
            
            // Hiển thị tổng số người dùng
            $total_message = get_string('total_users', 'block_mydata', count($users));
            echo html_writer::tag('p', $total_message, 
                array('style' => 'font-weight: bold; margin-bottom: 20px;'));
        }
        
        // Thêm nút quay lại
        echo html_writer::tag('div', 
            html_writer::link(new moodle_url('/blocks/mydata/report.php'), get_string('create_new_report', 'block_mydata'), 
                array('class' => 'btn btn-primary', 'style' => 'margin-top: 20px;')),
            array('style' => 'text-align: center;')
        );
    }
} else {
    // Hiển thị form
    echo html_writer::tag('div', 
        get_string('description_form', 'block_mydata'),
        array('class' => 'alert alert-info', 'style' => 'margin-bottom: 20px;')
    );
    
    $mform->display();
}

echo $OUTPUT->footer();