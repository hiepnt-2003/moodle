<?php
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/course/lib.php');

class local_clonecourse_external extends external_api {
    
    /**
     * Returns description of method parameters for get_courses_by_category
     */
    public static function get_courses_by_category_parameters() {
        return new external_function_parameters([
            'categoryid' => new external_value(PARAM_INT, 'Category ID', VALUE_REQUIRED),
        ]);
    }

    /**
     * Get all courses in a category
     */
    public static function get_courses_by_category($categoryid) {
        global $DB;
        
        // Validate parameters
        $params = self::validate_parameters(self::get_courses_by_category_parameters(), [
            'categoryid' => $categoryid
        ]);
        
        // Check if user has capability to view courses
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('moodle/course:view', $context);
        
        // Check if category exists
        if (!$DB->record_exists('course_categories', ['id' => $params['categoryid']])) {
            throw new invalid_parameter_exception('Category not found');
        }
        
        // Get courses in the category
        $courses = $DB->get_records('course', ['category' => $params['categoryid']], 'sortorder ASC');
        
        $result = [];
        foreach ($courses as $course) {
            // Skip site course
            if ($course->id == SITEID) {
                continue;
            }
            
            $result[] = [
                'id' => $course->id,
                'fullname' => $course->fullname,
                'shortname' => $course->shortname,
                'categoryid' => $course->category,
                'summary' => $course->summary,
                'summaryformat' => $course->summaryformat,
                'startdate' => $course->startdate,
                'enddate' => $course->enddate,
                'visible' => $course->visible,
                'format' => $course->format
            ];
        }
        
        return $result;
    }

    /**
     * Returns description of method result value for get_courses_by_category
     */
    public static function get_courses_by_category_returns() {
        return new external_multiple_structure(
            new external_single_structure([
                'id' => new external_value(PARAM_INT, 'Course ID'),
                'fullname' => new external_value(PARAM_TEXT, 'Course full name'),
                'shortname' => new external_value(PARAM_TEXT, 'Course short name'),
                'categoryid' => new external_value(PARAM_INT, 'Category ID'),
                'summary' => new external_value(PARAM_RAW, 'Course summary'),
                'summaryformat' => new external_value(PARAM_INT, 'Summary format'),
                'startdate' => new external_value(PARAM_INT, 'Course start date'),
                'enddate' => new external_value(PARAM_INT, 'Course end date'),
                'visible' => new external_value(PARAM_BOOL, 'Course visibility'),
                'format' => new external_value(PARAM_ALPHANUMEXT, 'Course format')
            ])
        );
    }

    /**
     * Returns description of method parameters for create_course
     */
    public static function create_course_parameters() {
        return new external_function_parameters([
            'course' => new external_single_structure([
                'fullname' => new external_value(PARAM_TEXT, 'Course full name', VALUE_REQUIRED),
                'shortname' => new external_value(PARAM_TEXT, 'Course short name', VALUE_REQUIRED),
                'categoryid' => new external_value(PARAM_INT, 'Category ID', VALUE_REQUIRED),
                'summary' => new external_value(PARAM_RAW, 'Course summary', VALUE_OPTIONAL, ''),
                'summaryformat' => new external_value(PARAM_INT, 'Summary format', VALUE_DEFAULT, FORMAT_HTML),
                'format' => new external_value(PARAM_ALPHANUMEXT, 'Course format', VALUE_DEFAULT, 'topics'),
                'startdate' => new external_value(PARAM_INT, 'Course start date', VALUE_OPTIONAL, 0),
                'enddate' => new external_value(PARAM_INT, 'Course end date', VALUE_OPTIONAL, 0),
                'visible' => new external_value(PARAM_BOOL, 'Course visibility', VALUE_DEFAULT, true)
            ])
        ]);
    }

    /**
     * Create a new course
     */
    public static function create_course($course) {
        global $DB, $CFG;
        
        // Validate parameters
        $params = self::validate_parameters(self::create_course_parameters(), ['course' => $course]);
        $course = $params['course'];
        
        // Check capabilities
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('moodle/course:create', $context);
        
        // Check if category exists
        if (!$DB->record_exists('course_categories', ['id' => $course['categoryid']])) {
            throw new invalid_parameter_exception('Category not found');
        }
        
        // Check if shortname already exists
        if ($DB->record_exists('course', ['shortname' => $course['shortname']])) {
            throw new invalid_parameter_exception('Course shortname already exists');
        }
        
        // Prepare course data
        $coursedata = new stdClass();
        $coursedata->fullname = $course['fullname'];
        $coursedata->shortname = $course['shortname'];
        $coursedata->category = $course['categoryid'];
        $coursedata->summary = $course['summary'];
        $coursedata->summaryformat = $course['summaryformat'];
        $coursedata->format = $course['format'];
        $coursedata->startdate = $course['startdate'];
        $coursedata->enddate = $course['enddate'];
        $coursedata->visible = $course['visible'] ? 1 : 0;
        
        // Create course
        $newcourse = create_course($coursedata);
        
        return [
            'id' => $newcourse->id,
            'fullname' => $newcourse->fullname,
            'shortname' => $newcourse->shortname,
            'categoryid' => $newcourse->category,
            'summary' => $newcourse->summary,
            'summaryformat' => $newcourse->summaryformat,
            'startdate' => $newcourse->startdate,
            'enddate' => $newcourse->enddate,
            'visible' => (bool)$newcourse->visible,
            'format' => $newcourse->format
        ];
    }

    /**
     * Returns description of method result value for create_course
     */
    public static function create_course_returns() {
        return new external_single_structure([
            'id' => new external_value(PARAM_INT, 'Course ID'),
            'fullname' => new external_value(PARAM_TEXT, 'Course full name'),
            'shortname' => new external_value(PARAM_TEXT, 'Course short name'),
            'categoryid' => new external_value(PARAM_INT, 'Category ID'),
            'summary' => new external_value(PARAM_RAW, 'Course summary'),
            'summaryformat' => new external_value(PARAM_INT, 'Summary format'),
            'startdate' => new external_value(PARAM_INT, 'Course start date'),
            'enddate' => new external_value(PARAM_INT, 'Course end date'),
            'visible' => new external_value(PARAM_BOOL, 'Course visibility'),
            'format' => new external_value(PARAM_ALPHANUMEXT, 'Course format')
        ]);
    }
}