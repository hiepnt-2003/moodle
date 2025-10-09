<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

namespace theme_th_boost\output;

use core\output\icon_system_fontawesome as core_icon_system_fontawesome;
use renderer_base;
use pix_icon;

defined('MOODLE_INTERNAL') || die();

/**
 * Class overriding the default icon system to use FontAwesome icons.
 *
 * @package    theme_th_boost
 * @copyright  2025
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class icon_system_fontawesome extends core_icon_system_fontawesome {

    /**
     * @var array $map Cached map of moodle icon names to FontAwesome icon names.
     */
    private $map = [];

    /**
     * Get the icon mapping.
     *
     * @return array
     */
    public function get_core_icon_map() {
        return [
            'core:i/navigationitem' => 'fa-circle',
            'core:i/course' => 'fa-graduation-cap',
            'core:i/dashboard' => 'fa-tachometer-alt',
            'core:i/admin' => 'fa-cog',
            'core:i/grades' => 'fa-chart-bar',
            'core:i/outcomes' => 'fa-tasks',
            'core:i/badge' => 'fa-certificate',
            'core:i/calendar' => 'fa-calendar',
            'core:i/email' => 'fa-envelope',
            'core:i/settings' => 'fa-cog',
            'core:i/user' => 'fa-user',
            'core:i/users' => 'fa-users',
            'core:i/group' => 'fa-users',
            'core:i/groupv' => 'fa-user-friends',
            'core:i/upload' => 'fa-upload',
            'core:i/download' => 'fa-download',
            'core:i/edit' => 'fa-pencil-alt',
            'core:i/delete' => 'fa-trash',
            'core:i/search' => 'fa-search',
            'core:i/valid' => 'fa-check',
            'core:i/invalid' => 'fa-times',
            'core:i/hide' => 'fa-eye-slash',
            'core:i/show' => 'fa-eye',
            'core:i/lock' => 'fa-lock',
            'core:i/unlock' => 'fa-unlock',
            'core:i/home' => 'fa-home',
            'core:i/help' => 'fa-question-circle',
            'core:i/info' => 'fa-info-circle',
            'core:i/warning' => 'fa-exclamation-triangle',
            'core:i/plus' => 'fa-plus',
            'core:i/minus' => 'fa-minus',
            'core:i/star' => 'fa-star',
            'core:i/star-o' => 'far fa-star',
            'core:i/folder' => 'fa-folder',
            'core:i/file' => 'fa-file',
            'core:t/message' => 'fa-comment',
            'core:t/edit' => 'fa-pencil-alt',
            'core:t/delete' => 'fa-trash',
            'core:t/hide' => 'fa-eye-slash',
            'core:t/show' => 'fa-eye',
            'core:t/preview' => 'fa-eye',
            'core:t/copy' => 'fa-copy',
            'core:t/download' => 'fa-download',
            'core:t/up' => 'fa-arrow-up',
            'core:t/down' => 'fa-arrow-down',
            'core:t/left' => 'fa-arrow-left',
            'core:t/right' => 'fa-arrow-right',
            'core:t/check' => 'fa-check',
            'core:t/add' => 'fa-plus',
            'core:t/removeall' => 'fa-times',
            'core:a/logout' => 'fa-sign-out-alt',
            'core:a/help' => 'fa-question-circle',
            'core:a/search' => 'fa-search',
            'core:a/refresh' => 'fa-sync',
        ];
    }

    /**
     * Render a pix_icon.
     *
     * @param renderer_base $output
     * @param pix_icon $icon
     * @return string
     */
    public function render_pix_icon(renderer_base $output, pix_icon $icon) {
        // Get the icon mapping.
        if (empty($this->map)) {
            $this->map = $this->get_core_icon_map();
        }

        $iconname = $icon->get_component() . ':' . $icon->get_name();
        
        // Check if we have a FontAwesome mapping.
        if (isset($this->map[$iconname])) {
            $faicon = $this->map[$iconname];
            $attributes = $icon->get_attributes();
            
            $title = '';
            if (isset($attributes['title'])) {
                $title = $attributes['title'];
            } else if (isset($attributes['alt'])) {
                $title = $attributes['alt'];
            }
            
            $classes = 'icon fa ' . $faicon;
            if (!empty($attributes['class'])) {
                $classes .= ' ' . $attributes['class'];
            }
            
            return \html_writer::tag('i', '', [
                'class' => $classes,
                'title' => $title,
                'aria-hidden' => 'true'
            ]);
        }
        
        // Fall back to parent implementation.
        return parent::render_pix_icon($output, $icon);
    }
}
