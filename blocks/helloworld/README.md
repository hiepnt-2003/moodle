# Hello World Block Plugin for Moodle

A simple Moodle block plugin that displays a customizable greeting message to users.

## Features

- Displays a personalized greeting message
- Configurable title and message content
- Support for placeholders: `{username}` and `{coursename}`
- Optional current date display
- Multiple instances allowed
- Works on course pages, site pages, modules, and user dashboard

## Installation

1. Copy the `helloworld` folder to your Moodle installation's `blocks/` directory
2. Visit your Moodle site as an administrator
3. Navigate to Site Administration → Notifications
4. Complete the installation process

## Configuration

### Block Instance Configuration

Each block instance can be configured individually:

1. Turn editing on
2. Add the "Hello World" block to your page
3. Click the gear icon on the block and select "Configure Hello World block"
4. Configure the following settings:
   - **Block title**: The title displayed at the top of the block
   - **Message**: The greeting message (supports placeholders)
   - **Show current date**: Whether to display the current date

### Placeholders

You can use the following placeholders in your message:
- `{username}` - Displays the full name of the current user
- `{coursename}` - Displays the name of the current course

Example message: "Hello {username}! Welcome to {coursename}!"

## File Structure

```
blocks/helloworld/
├── block_helloworld.php      # Main block class
├── version.php               # Plugin version information
├── edit_form.php             # Configuration form
├── renderer.php              # Output renderer
├── lib.php                   # Library functions
├── settings.php              # Global settings (currently empty)
├── README.md                 # This file
├── db/
│   └── access.php           # Permission definitions
├── lang/
│   └── en/
│       └── block_helloworld.php  # English language strings
└── templates/
    └── content.mustache     # Template for block content
```

## Capabilities

- `block/helloworld:addinstance` - Add a new Hello World block to pages
- `block/helloworld:myaddinstance` - Add a new Hello World block to Dashboard

## Requirements

- Moodle 4.1 or later
- PHP 7.4 or later

## Development

### Customizing the Template

The block uses a Mustache template located at `templates/content.mustache`. You can modify this template to change the appearance of the block content.

### Adding New Features

To add new configuration options:

1. Add the field to `edit_form.php`
2. Add corresponding language strings to `lang/en/block_helloworld.php`
3. Update the `get_content()` method in `block_helloworld.php`
4. Modify the template if needed

## License

This plugin is licensed under the GNU GPL v3 or later.

## Support

For support, please create an issue in the project repository or contact the plugin maintainer.

## Version History

- v1.0 (2025-09-24): Initial release
  - Basic greeting functionality
  - Configurable title and message
  - Placeholder support for username and course name
  - Optional date display