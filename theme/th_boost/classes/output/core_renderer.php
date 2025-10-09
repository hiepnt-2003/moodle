<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

namespace theme_th_boost\output;

use html_writer;

defined('MOODLE_INTERNAL') || die;

/**
 * Core renderer for TH Boost theme.
 *
 * @package    theme_th_boost
 * @copyright  2025
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_renderer extends \theme_boost\output\core_renderer {

    /**
     * Override to add FontAwesome and password toggle functionality.
     *
     * @return string HTML to output.
     */
    public function standard_end_of_body_html() {
        $output = parent::standard_end_of_body_html();
        
        // Add FontAwesome CDN.
        $output .= html_writer::script('', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js');
        
        // Add password toggle script.
        $output .= html_writer::script('
            document.addEventListener("DOMContentLoaded", function() {
                // Find password input fields
                const passwordFields = document.querySelectorAll(\'input[type="password"]\');
                
                passwordFields.forEach(function(passwordField) {
                    // Skip if already wrapped
                    if (passwordField.parentElement.classList.contains("password-toggle-container")) {
                        return;
                    }
                    
                    // Create wrapper
                    const wrapper = document.createElement("div");
                    wrapper.className = "password-toggle-container";
                    
                    // Wrap the password field
                    passwordField.parentNode.insertBefore(wrapper, passwordField);
                    wrapper.appendChild(passwordField);
                    
                    // Create toggle button
                    const toggleBtn = document.createElement("button");
                    toggleBtn.type = "button";
                    toggleBtn.className = "password-toggle-btn";
                    toggleBtn.innerHTML = \'<i class="far fa-eye" aria-hidden="true"></i>\';
                    toggleBtn.setAttribute("aria-label", "Toggle password visibility");
                    toggleBtn.setAttribute("title", "Show password");
                    
                    // Add toggle button after password field
                    wrapper.appendChild(toggleBtn);
                    
                    // Add click event
                    toggleBtn.addEventListener("click", function() {
                        const type = passwordField.getAttribute("type") === "password" ? "text" : "password";
                        passwordField.setAttribute("type", type);
                        
                        // Toggle icon
                        const icon = toggleBtn.querySelector("i");
                        if (type === "text") {
                            icon.className = "far fa-eye-slash";
                            toggleBtn.setAttribute("title", "Hide password");
                        } else {
                            icon.className = "far fa-eye";
                            toggleBtn.setAttribute("title", "Show password");
                        }
                    });
                });
            });
        ');
        
        return $output;
    }

    /**
     * Override to add FontAwesome support in head.
     *
     * @return string HTML fragment.
     */
    public function standard_head_html() {
        $output = parent::standard_head_html();
        
        // Add FontAwesome CSS (for solid icons that don't need JS).
        $output .= html_writer::tag('link', '', [
            'rel' => 'stylesheet',
            'href' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
            'integrity' => 'sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==',
            'crossorigin' => 'anonymous',
            'referrerpolicy' => 'no-referrer'
        ]);
        
        return $output;
    }
}
