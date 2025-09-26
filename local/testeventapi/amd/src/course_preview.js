// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Course preview functionality for Test Event API plugin.
 *
 * @module     local_testeventapi/course_preview
 * @copyright  2025 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/ajax', 'core/notification'], function($, Ajax, Notification) {
    
    /**
     * Initialize course preview functionality.
     */
    function init() {
        // Listen for changes to the start date field.
        $('#id_start_date_day, #id_start_date_month, #id_start_date_year, #id_start_date_hour, #id_start_date_minute')
            .on('change', function() {
                updateCoursePreview();
            });
        
        // Initial preview load if date is already set.
        updateCoursePreview();
    }
    
    /**
     * Update the course preview based on selected date.
     */
    function updateCoursePreview() {
        var previewDiv = $('#course-preview');
        if (previewDiv.length === 0) {
            return;
        }
        
        // Get the selected date components.
        var day = $('#id_start_date_day').val();
        var month = $('#id_start_date_month').val();  
        var year = $('#id_start_date_year').val();
        var hour = $('#id_start_date_hour').val();
        var minute = $('#id_start_date_minute').val();
        
        // Check if all date components are selected.
        if (!day || !month || !year || !hour || !minute) {
            previewDiv.html('<div class="alert alert-info">Chọn ngày bắt đầu để xem preview các môn học sẽ được thêm tự động.</div>');
            return;
        }
        
        // Show loading.
        previewDiv.html('<div class="alert alert-info"><span class="loading"></span> Đang tải preview môn học...</div>');
        
        // Create timestamp from selected date.
        var selectedDate = new Date(year, month - 1, day, hour, minute);
        var timestamp = Math.floor(selectedDate.getTime() / 1000);
        
        // Call AJAX to get matching courses.
        var request = Ajax.call([{
            methodname: 'local_testeventapi_get_courses_by_date',
            args: {
                startdate: timestamp
            }
        }]);
        
        request[0].done(function(response) {
            displayCoursePreview(response.courses, selectedDate);
        }).fail(function(error) {
            // Fallback for AJAX error - just show basic message.
            previewDiv.html('<div class="alert alert-warning">Không thể tải preview. Ngày đã chọn: ' + 
                selectedDate.toLocaleDateString('vi-VN') + '</div>');
        });
    }
    
    /**
     * Display the course preview.
     * 
     * @param {Array} courses List of matching courses
     * @param {Date} selectedDate The selected date
     */
    function displayCoursePreview(courses, selectedDate) {
        var previewDiv = $('#course-preview');
        var dateStr = selectedDate.toLocaleDateString('vi-VN');
        
        if (courses.length === 0) {
            previewDiv.html('<div class="alert alert-warning">Không có môn học nào bắt đầu vào ngày ' + 
                dateStr + ' sẽ được thêm tự động.</div>');
            return;
        }
        
        var html = '<div class="alert alert-success">';
        html += '<strong>Tìm thấy ' + courses.length + ' môn học bắt đầu vào ngày ' + dateStr + ':</strong>';
        html += '<div class="course-preview-list mt-2">';
        
        courses.forEach(function(course) {
            html += '<div class="course-preview-item">';
            html += '<strong>' + course.fullname + '</strong> (' + course.shortname + ')';
            if (course.startdate_formatted) {
                html += '<br><small class="text-muted">Bắt đầu: ' + course.startdate_formatted + '</small>';
            }
            html += '</div>';
        });
        
        html += '</div>';
        html += '<small class="text-muted mt-2 d-block">Các môn học này sẽ được tự động thêm vào đợt khi tạo.</small>';
        html += '</div>';
        
        previewDiv.html(html);
    }
    
    return {
        init: init
    };
});