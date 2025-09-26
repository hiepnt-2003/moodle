<?php
defined('MOODLE_INTERNAL') || die();

/**
 * Adds local_hello links to the navigation
 *
 * @param global_navigation $navigation
 */
function local_hello_extend_navigation(global_navigation $navigation) {
    global $PAGE;
    
    // Only add to site context
    if ($PAGE->context->contextlevel == CONTEXT_SYSTEM) {
        $node = $navigation->add(
            get_string('pluginname', 'local_hello'),
            new moodle_url('/local/hello/index.php'),
            navigation_node::TYPE_CUSTOM,
            null,
            'local_hello',
            new pix_icon('i/settings', '')
        );
        $node->showinflatnavigation = true;
    }
}

/**
 * Add navigation nodes to the settings navigation
 *
 * @param settings_navigation $navigation
 * @param context $context
 */
function local_hello_extend_settings_navigation(settings_navigation $navigation, context $context) {
    global $PAGE;
    
    // Only add to site context
    if ($context->contextlevel == CONTEXT_SYSTEM && has_capability('local/hello:view', $context)) {
        if ($settingnode = $navigation->find('localplugins', navigation_node::TYPE_SETTING)) {
            $url = new moodle_url('/local/hello/index.php');
            $node = navigation_node::create(
                get_string('pluginname', 'local_hello'),
                $url,
                navigation_node::TYPE_SETTING,
                null,
                'local_hello',
                new pix_icon('i/settings', '')
            );
            $settingnode->add_node($node);
        }
    }
}