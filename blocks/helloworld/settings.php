<?php
defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('block_helloworld_settings', get_string('pluginname', 'block_helloworld'));

    $settings->add(new admin_setting_configtext(
        'block_helloworld/defaultmessage',
        get_string('defaultmessage', 'block_helloworld'),
        get_string('defaultmessage_desc', 'block_helloworld'),
        'Hello Moodle!'
    ));

    $ADMIN->add('blocks', $settings);
}
