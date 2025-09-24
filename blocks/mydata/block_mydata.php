<?php
class block_mydata extends block_base {
    public function init() {
        $this->title = get_string('pluginname', 'block_mydata');
    }

    public function get_content() {
        global $DB, $OUTPUT;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();
        $content = '';

        // 🔹 Lấy danh sách courses
        $courses = $DB->get_records('course', null, 'fullname ASC',
            'id, fullname, shortname, startdate, enddate, category, visible');

        $content .= html_writer::tag('h4', 'Danh sách khóa học');
        $content .= html_writer::start_tag('table', ['class' => 'generaltable']);
        $content .= html_writer::start_tag('tr');
        $headers = ['Fullname', 'Shortname', 'Start date', 'End date', 'Category', 'Visible'];
        foreach ($headers as $h) {
            $content .= html_writer::tag('th', $h);
        }
        $content .= html_writer::end_tag('tr');

        foreach ($courses as $c) {
            $content .= html_writer::start_tag('tr');
            $content .= html_writer::tag('td', $c->fullname);
            $content .= html_writer::tag('td', $c->shortname);
            $content .= html_writer::tag('td', userdate($c->startdate));
            $content .= html_writer::tag('td', userdate($c->enddate));
            $content .= html_writer::tag('td', $c->category);
            $content .= html_writer::tag('td', $c->visible ? 'Yes' : 'No');
            $content .= html_writer::end_tag('tr');
        }
        $content .= html_writer::end_tag('table');

        // 🔹 Lấy danh sách users
        $users = $DB->get_records('user', null, 'lastname ASC',
            'id, username, firstname, lastname, email');

        $content .= html_writer::tag('h4', 'Danh sách người dùng');
        $content .= html_writer::start_tag('table', ['class' => 'generaltable']);
        $content .= html_writer::start_tag('tr');
        $headers = ['Username', 'Full name', 'Email'];
        foreach ($headers as $h) {
            $content .= html_writer::tag('th', $h);
        }
        $content .= html_writer::end_tag('tr');

        foreach ($users as $u) {
            // Bỏ user guest và admin (id = 1,2)
            if ($u->id <= 2) continue;

            $fullname = fullname($u);
            $content .= html_writer::start_tag('tr');
            $content .= html_writer::tag('td', $u->username);
            $content .= html_writer::tag('td', $fullname);
            $content .= html_writer::tag('td', $u->email);
            $content .= html_writer::end_tag('tr');
        }
        $content .= html_writer::end_tag('table');

        $this->content->text = $content;
        return $this->content;
    }
}
