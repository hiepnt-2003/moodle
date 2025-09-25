<?php
$capabilities = [
    'block/mydata:myaddinstance' => [
        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => [
            'user' => CAP_ALLOW
        ],
        'clonepermissionsfrom' => 'moodle/my:manageblocks'
    ],

    'block/mydata:addinstance' => [
        'riskbitmask' => RISK_SPAM | RISK_XSS,
        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => [
            'coursecreator' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        ],
        'clonepermissionsfrom' => 'moodle/site:manageblocks'
    ],

    'block/mydata:viewreports' => [
        'captype' => 'read',
        'contextlevel' => 50, // CONTEXT_SYSTEM
        'archetypes' => [
            'manager' => 1, // CAP_ALLOW
            'coursecreator' => 1 // CAP_ALLOW - Course Creator cũng có quyền manager
        ]
    ],
];
