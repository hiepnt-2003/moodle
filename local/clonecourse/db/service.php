<?php
defined('MOODLE_INTERNAL') || die();

$functions = [
    'local_clonecourse_get_courses_by_category' => [
        'classname'     => 'local_clonecourse_external',
        'methodname'    => 'get_courses_by_category',
        'classpath'     => 'local/clonecourse/externallib.php',
        'description'   => 'Get all courses in a category',
        'type'          => 'read',
        'ajax'          => true,
        'capabilities'  => 'moodle/course:view',
    ],
    'local_clonecourse_create_course' => [
        'classname'     => 'local_clonecourse_external',
        'methodname'    => 'create_course',
        'classpath'     => 'local/clonecourse/externallib.php',
        'description'   => 'Create a new course in a category',
        'type'          => 'write',
        'ajax'          => true,
        'capabilities'  => 'moodle/course:create',
    ]
];

$services = [
    'Clone Course Service' => [
        'functions' => [
            'local_clonecourse_get_courses_by_category',
            'local_clonecourse_create_course'
        ],
        'restrictedusers' => 0,
        'enabled' => 1,
        'shortname' => 'clonecourse',
    ],
];