<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

defined('MOODLE_INTERNAL') || die();

$THEME->name = 'th_boost';
$THEME->sheets = [];
$THEME->editor_sheets = [];
$THEME->parents = ['boost'];
$THEME->enable_dock = false;
$THEME->yuicssmodules = [];
$THEME->rendererfactory = 'theme_overridden_renderer_factory';
$THEME->requiredblocks = '';
$THEME->addblockposition = BLOCK_ADDBLOCK_POSITION_FLATNAV;
// Icon system removed for better compatibility with Moodle 3.8/3.9
// FontAwesome still available via CDN in templates
$THEME->prescsscallback = 'theme_th_boost_get_pre_scss';
$THEME->scss = function($theme) {
    return theme_th_boost_get_main_scss_content($theme);
};
$THEME->extrascsscallback = 'theme_th_boost_get_extra_scss';
$THEME->precompiledcsscallback = 'theme_th_boost_get_precompiled_css';
$THEME->usefallback = true;
$THEME->haseditswitch = true;
