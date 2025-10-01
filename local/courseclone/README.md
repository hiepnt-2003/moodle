# Course Clone Plugin

This plugin provides a webservice to clone Moodle courses with new details.

## Features

- Clone existing courses via webservice
- Set new course details (fullname, shortname, start date, end date)
- Returns status, course ID, and success/error messages
- Full backup and restore functionality

## Installation

1. Copy the plugin to `/local/courseclone/` in your Moodle installation
2. Visit Site Administration → Notifications to complete the installation
3. Enable web services in Site Administration → Advanced features
4. Configure the webservice in Site Administration → Server → Web services

## Usage

### Webservice Function: `local_courseclone_clone_course`

#### Input Parameters:
- `shortname_clone` (string) - Shortname of the source course to clone
- `fullname` (string) - Full name for the new course  
- `shortname` (string) - Short name for the new course
- `startdate` (int) - Start date timestamp for the new course
- `enddate` (int) - End date timestamp for the new course

#### Output:
- `status` (string) - "success" or "error"
- `id` (int) - ID of the cloned course (0 if error)
- `message` (string) - Success message or error description

#### Example JSON Request:
```json
{
    "shortname_clone": "course1",
    "fullname": "New Course Name", 
    "shortname": "newcourse1",
    "startdate": 1704067200,
    "enddate": 1735689600
}
```

#### Example Response (Success):
```json
{
    "status": "success",
    "id": 123,
    "message": "Course cloned successfully"
}
```

#### Example Response (Error):
```json
{
    "status": "error", 
    "id": 0,
    "message": "Source course with shortname 'course1' not found"
}
```

## Testing with Postman

1. Set up authentication token in Moodle
2. Use POST method to `/webservice/rest/server.php`
3. Include required parameters:
   - `wstoken` - Your webservice token
   - `wsfunction` - `local_courseclone_clone_course`
   - `moodlewsrestformat` - `json`
   - Include all required function parameters

## Requirements

- Moodle 4.1 or higher
- Proper capabilities: `moodle/course:create`, `moodle/backup:backupcourse`, `moodle/restore:restorecourse`

## License

GPL v3 or later