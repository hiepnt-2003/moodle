<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    // Boost provides a nice setting page which splits settings onto separate tabs.
    $settings = new theme_boost_admin_settingspage_tabs('themesettingth_boost', get_string('configtitle', 'theme_th_boost'));
    $page = new admin_settingpage('theme_th_boost_general', get_string('generalsettings', 'theme_th_boost'));

    // Preset.
    $name = 'theme_th_boost/preset';
    $title = get_string('preset', 'theme_th_boost');
    $description = get_string('preset_desc', 'theme_th_boost');
    $default = 'default.scss';

    $context = context_system::instance();
    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'theme_th_boost', 'preset', 0, 'itemid, filepath, filename', false);

    $choices = [];
    foreach ($files as $file) {
        $choices[$file->get_filename()] = $file->get_filename();
    }
    $choices['default.scss'] = 'default.scss';
    $choices['plain.scss'] = 'plain.scss';

    $setting = new admin_setting_configthemepreset($name, $title, $description, $default, $choices, 'th_boost');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Preset files setting.
    $name = 'theme_th_boost/presetfiles';
    $title = get_string('presetfiles', 'theme_th_boost');
    $description = get_string('presetfiles_desc', 'theme_th_boost');

    $setting = new admin_setting_configstoredfile($name, $title, $description, 'preset', 0,
        array('maxfiles' => 20, 'accepted_types' => array('.scss')));
    $page->add($setting);

    // Background image setting.
    $name = 'theme_th_boost/backgroundimage';
    $title = get_string('backgroundimage', 'theme_th_boost');
    $description = get_string('backgroundimage_desc', 'theme_th_boost');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'backgroundimage');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Login page background image.
    $name = 'theme_th_boost/loginbackgroundimage';
    $title = get_string('loginbackgroundimage', 'theme_th_boost');
    $description = get_string('loginbackgroundimage_desc', 'theme_th_boost');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'loginbackgroundimage');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Variable $brand-color.
    $name = 'theme_th_boost/brandcolor';
    $title = get_string('brandcolor', 'theme_th_boost');
    $description = get_string('brandcolor_desc', 'theme_th_boost');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Must add the page after defining all the settings!
    $settings->add($page);

    // Advanced settings.
    $page = new admin_settingpage('theme_th_boost_advanced', get_string('advancedsettings', 'theme_th_boost'));

    // Raw SCSS to include before the content.
    $setting = new admin_setting_scsscode('theme_th_boost/scsspre',
        get_string('rawscsspre', 'theme_th_boost'), get_string('rawscsspre_desc', 'theme_th_boost'), '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Raw SCSS to include after the content.
    $setting = new admin_setting_scsscode('theme_th_boost/scss', get_string('rawscss', 'theme_th_boost'),
        get_string('rawscss_desc', 'theme_th_boost'), '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $settings->add($page);
}
