<?php
    require_once('../../config.php');
    require_once($CFG->dirroot . '/blocks/mydata/lib.php');

    defined('MOODLE_INTERNAL') || die();

    // Require login
    require_login();

    // Set up the page
    $PAGE->set_url('/blocks/mydata/view.php');
    $PAGE->set_context(context_system::instance());
    $PAGE->set_title(get_string('view_all_title', 'block_mydata'));
    $PAGE->set_heading(get_string('view_all_heading', 'block_mydata'));
    $PAGE->set_pagelayout('standard');

    echo $OUTPUT->header();

    // Check Admin or Manager permission
    if (!block_mydata_has_access_permission()) {
        print_error('nopermissions', 'error', '', get_string('no_permission_error', 'block_mydata'));
    }

    // Get courses list with category information
    $sql = "SELECT c.id, c.fullname, c.shortname, c.startdate, c.enddate, cc.name as categoryname
            FROM {course} c
            LEFT JOIN {course_categories} cc ON c.category = cc.id
            WHERE c.id != 1
            ORDER BY c.fullname ASC";
    $courses = $DB->get_records_sql($sql);

    // Prepare courses data for template
    $coursesdata = array();
    $coursesdata['hascourses'] = !empty($courses);
    $coursesdata['courses'] = array();
    
    $stt = 1;
    foreach ($courses as $c) {
        $coursedata = array();
        $coursedata['stt'] = $stt;
        $coursedata['fullname'] = $c->fullname;
        $coursedata['shortname'] = $c->shortname;
        $coursedata['courseurl'] = new moodle_url('/course/view.php', array('id' => $c->id));
        $coursedata['startdate'] = $c->startdate > 0 ? date('d/m/Y', $c->startdate) : get_string('nodate', 'block_mydata');
        $coursedata['enddate'] = $c->enddate > 0 ? date('d/m/Y', $c->enddate) : get_string('nodate', 'block_mydata');
        $coursedata['categoryname'] = $c->categoryname ? $c->categoryname : get_string('nocategory', 'block_mydata');
        
        $coursesdata['courses'][] = $coursedata;
        $stt++;
    }

    // Render courses template
    echo $OUTPUT->render_from_template('block_mydata/courses_list', $coursesdata);

    // Get users list
    $users = $DB->get_records('user', array('deleted' => 0), 'lastname ASC',
        'id, username, firstname, lastname, email');

    // Prepare users data for template
    $usersdata = array();
    $usersdata['hasusers'] = false;
    $usersdata['users'] = array();
    
    $stt = 1;
    foreach ($users as $u) {
        // Skip guest and admin users (id = 1,2)
        if ($u->id <= 2) continue;

        $usersdata['hasusers'] = true;
        $userdata = array();
        $userdata['stt'] = $stt;
        $userdata['username'] = $u->username;
        $userdata['fullname'] = fullname($u);
        $userdata['email'] = $u->email;
        $userdata['userprofileurl'] = new moodle_url('/user/profile.php', array('id' => $u->id));
        
        $usersdata['users'][] = $userdata;
        $stt++;
    }

    // Render users template
    echo $OUTPUT->render_from_template('block_mydata/users_list', $usersdata);

    // Render navigation bar template
    $navdata = array();
    $navdata['reporturl'] = new moodle_url('/blocks/mydata/report.php');
    $navdata['viewurl'] = new moodle_url('/blocks/mydata/view.php');
    
    echo $OUTPUT->render_from_template('block_mydata/navigation_bar', $navdata);

    echo $OUTPUT->footer();