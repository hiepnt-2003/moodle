# Moodle Local Plugin: Create Table Manager

A Moodle local plugin for managing batch course enrollment with automatic course assignment based on start dates.

## Features

- **Batch Management**: Create and manage course enrollment batches
- **Automatic Course Assignment**: Automatically assign courses to batches based on matching start dates
- **Template-based UI**: Modern Mustache template-based interface
- **Responsive Design**: Mobile-friendly CSS design
- **Statistics Dashboard**: Visual statistics showing auto vs manual course assignments
- **Multilingual Support**: Full internationalization support (Vietnamese included)

## Requirements

- Moodle 3.10 or higher
- PHP 7.4 or higher

## Installation

1. Extract the plugin to `your-moodle/local/createtable/`
2. Login as administrator and go to Site Administration
3. Follow the installation prompts
4. Configure permissions as needed

## Usage

### Creating Batches

1. Navigate to Site Administration > Local plugins > Create Table Manager
2. Click "Add New Batch"
3. Enter batch name and open date
4. Courses with matching start dates will be automatically added

### Managing Courses

- View batch details to see all assigned courses
- Green badges indicate automatically assigned courses
- Orange badges indicate manually assigned courses
- Use "Refresh Auto-courses" to update automatic assignments

## File Structure

```
local/createtable/
├── amd/                          # AMD JavaScript modules
│   └── src/
│       └── batch_form.js         # Form validation module
├── backup/                       # Backup and restore (future)
├── classes/
│   ├── batch_manager.php         # Core business logic
│   ├── output/
│   │   └── renderer.php          # Template data preparation
│   └── privacy/
│       └── provider.php          # GDPR privacy provider
├── db/
│   ├── access.php                # Capabilities definition
│   ├── install.xml               # Database schema
│   └── upgrade.php               # Database upgrade script
├── lang/
│   └── en/
│       └── local_createtable.php # English language strings
├── styles/
│   └── styles.css                # Plugin CSS styles
├── templates/
│   ├── batch_list.mustache       # Batch listing template
│   ├── batch_detail.mustache     # Batch detail template
│   └── batch_form.mustache       # Batch form template
├── tests/
│   └── batch_manager_test.php    # Unit tests
├── index.php                     # Main page
├── lib.php                       # Helper functions
├── manage.php                    # Batch management page
├── refresh_courses.php           # Auto-course refresh
├── settings.php                  # Admin settings
├── version.php                   # Plugin version info
├── view.php                      # Batch detail page
└── README.md                     # Documentation
```

## Database Schema

### Tables

- `local_createtable_batches`: Store batch information
- `local_createtable_courses`: Store course-batch relationships

## Capabilities

- `moodle/site:config`: Required to access and manage batches

## Scheduled Tasks

### Monthly Course Creation
- **Task**: `local_createtable\task\monthly_course_creation`
- **Schedule**: 5:00 AM on 1st day of each month
- **Function**: Automatically creates a new batch and adds matching courses
- **Requirements**: Auto-assign must be enabled in plugin settings

To manage the scheduled task:
1. Go to **Site Administration → Server → Scheduled tasks**
2. Find "Tạo đợt môn học hàng tháng" task
3. Configure timing if needed (default: 0 5 1 * *)

## License

This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

## Author

Your Name <your.email@example.com>

## Changelog

### v1.2 (2025-09-26)
- Refactored code structure to follow Moodle coding standards
- Improved documentation and comments
- Enhanced error handling
- Added proper namespacing

### v1.1 (2025-09-25)
- Added automatic course assignment based on start dates
- Implemented statistics dashboard
- Added template-based UI with Mustache
- Responsive CSS design
- Multilingual support

### v1.0 (2025-09-24)
- Initial release
- Basic batch management functionality