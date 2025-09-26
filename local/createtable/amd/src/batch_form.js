/**
 * Batch form validation and interactions
 *
 * @module     local_createtable/batch_form
 * @copyright  2025
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/str', 'core/notification'], function($, str, notification) {
    'use strict';

    /**
     * Initialize batch form functionality
     */
    var init = function() {
        var form = $('.local-createtable-form');
        
        if (form.length === 0) {
            return;
        }

        // Form validation
        form.on('submit', function(e) {
            if (!validateForm()) {
                e.preventDefault();
                return false;
            }
        });

        // Real-time validation feedback
        $('#batch_name').on('blur', function() {
            validateBatchName();
        });

        $('#open_date').on('blur', function() {
            validateOpenDate();
        });

        // Enhanced date picker for better UX
        enhanceDatePicker();
    };

    /**
     * Validate the entire form
     * @return {boolean} True if form is valid
     */
    var validateForm = function() {
        var isValid = true;
        
        if (!validateBatchName()) {
            isValid = false;
        }
        
        if (!validateOpenDate()) {
            isValid = false;
        }
        
        return isValid;
    };

    /**
     * Validate batch name field
     * @return {boolean} True if valid
     */
    var validateBatchName = function() {
        var nameField = $('#batch_name');
        var name = nameField.val().trim();
        
        clearFieldError(nameField);
        
        if (!name) {
            showFieldError(nameField, str.get_string('batchname_required', 'local_createtable'));
            return false;
        }
        
        if (name.length > 255) {
            showFieldError(nameField, str.get_string('batchname_toolong', 'local_createtable'));
            return false;
        }
        
        showFieldSuccess(nameField);
        return true;
    };

    /**
     * Validate open date field
     * @return {boolean} True if valid
     */
    var validateOpenDate = function() {
        var dateField = $('#open_date');
        var dateValue = dateField.val();
        
        clearFieldError(dateField);
        
        if (!dateValue) {
            showFieldError(dateField, str.get_string('opendate_required', 'local_createtable'));
            return false;
        }
        
        var selectedDate = new Date(dateValue);
        var today = new Date();
        today.setHours(0, 0, 0, 0);
        
        if (selectedDate < today) {
            showFieldError(dateField, str.get_string('opendate_past', 'local_createtable'));
            return false;
        }
        
        showFieldSuccess(dateField);
        return true;
    };

    /**
     * Show error message for a field
     * @param {jQuery} field The form field
     * @param {Promise} messagePromise Promise that resolves to error message
     */
    var showFieldError = function(field, messagePromise) {
        messagePromise.then(function(message) {
            field.addClass('is-invalid');
            var errorElement = field.siblings('.invalid-feedback');
            if (errorElement.length === 0) {
                errorElement = $('<div class="invalid-feedback"></div>');
                field.after(errorElement);
            }
            errorElement.text(message).show();
        }).catch(function() {
            // Fallback if string loading fails
            field.addClass('is-invalid');
        });
    };

    /**
     * Show success state for a field
     * @param {jQuery} field The form field
     */
    var showFieldSuccess = function(field) {
        field.removeClass('is-invalid').addClass('is-valid');
        field.siblings('.invalid-feedback').hide();
    };

    /**
     * Clear error state for a field
     * @param {jQuery} field The form field
     */
    var clearFieldError = function(field) {
        field.removeClass('is-invalid is-valid');
        field.siblings('.invalid-feedback').hide();
    };

    /**
     * Enhance date picker with better UX
     */
    var enhanceDatePicker = function() {
        var dateField = $('#open_date');
        
        // Set minimum date to today
        var today = new Date();
        var todayStr = today.getFullYear() + '-' + 
                      String(today.getMonth() + 1).padStart(2, '0') + '-' + 
                      String(today.getDate()).padStart(2, '0');
        dateField.attr('min', todayStr);
        
        // Add helpful placeholder
        dateField.attr('title', str.get_string('opendate_help', 'local_createtable'));
    };

    return {
        init: init
    };
});