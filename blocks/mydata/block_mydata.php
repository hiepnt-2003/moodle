<?php

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/blocks/mydata/lib.php');

class block_mydata extends block_base {
    public function init() {
        $this->title = get_string('pluginname', 'block_mydata');
    }

    public function get_content() {
        global $DB, $OUTPUT, $CFG, $PAGE;

        if ($this->content !== null) {
            return $this->content;
        }

        // Load CSS file
        $PAGE->requires->css('/blocks/mydata/styles/styles.css');

        $this->content = new stdClass();
        
        // Use permission check function from lib.php
        if (block_mydata_has_access_permission()) {
            // If has admin or manager permission, show links
            $view_url = new moodle_url('/blocks/mydata/view.php');
            $report_url = new moodle_url('/blocks/mydata/report.php');
            
            $view_link = html_writer::link($view_url, get_string('view_all', 'block_mydata'), 
                ['class' => 'btn btn-primary']);
            $report_link = html_writer::link($report_url, get_string('course_report', 'block_mydata'), 
                ['class' => 'btn btn-secondary']);
            
            $content = html_writer::tag('div', 
                html_writer::tag('div', 
                    $view_link . ' ' . $report_link, 
                    ['class' => 'btn-group text-center']
                ) .
                html_writer::tag('p', get_string('description_navigation', 'block_mydata'), 
                    ['class' => 'text-center text-muted', 'style' => 'font-size:12px; margin-top:10px;']),
                ['class' => 'block_mydata']
            );
        } else {
            // If no permission, show message
            $content = html_writer::tag('div', 
                html_writer::tag('p', 
                    '<i class="fa fa-lock"></i> ' . get_string('no_permission', 'block_mydata'), 
                    ['class' => 'text-center']
                ), 
                ['class' => 'block_mydata alert alert-warning']
            );
        }

        $this->content->text = $content;
        return $this->content;
    }
}
