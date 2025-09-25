<?php<?php

// This file is part of Moodle - http://moodle.org/// This file is part of Moodle - http://moodle.org/

////

// Moodle is free software: you can redistribute it and/or modify// Moodle is free software: you can redistribute it and/or modify

// it under the terms of the GNU General Public License as published by// it under the terms of the GNU General Public License as published by

// the Free Software Foundation, either version 3 of the License, or// the Free Software Foundation, either version 3 of the License, or

// (at your option) any later version.// (at your option) any later version.

////

// Moodle is distributed in the hope that it will be useful,// Moodle is distributed in the hope that it will be useful,

// but WITHOUT ANY WARRANTY; without even the implied warranty of// but WITHOUT ANY WARRANTY; without even the implied warranty of

// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the

// GNU General Public License for more details.// GNU General Public License for more details.

////

// You should have received a copy of the GNU General Public License// You should have received a copy of the GNU General Public License

// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.



/**/**

 * Version information for local_course_batches plugin * Version information for local_course_batches plugin

 * *

 * @package    local_course_batches * @package    local_course_batches

 * @copyright  2025 Your Name * @copyright  2025 Your Name

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

 */ */



defined('MOODLE_INTERNAL') || die();defined('MOODLE_INTERNAL') || die();



$plugin->component = 'local_course_batches';$plugin->component = 'local_course_batches';

$plugin->version = 2025092504; // Version 2025-09-25 v4$plugin->version = 2025092503; // Build của plugin theo ngày hiện tại (cấu trúc đơn giản hóa)

$plugin->requires = 2020110900; // Moodle 3.10$plugin->requires = 2020110900; // Số build tương ứng Moodle 3.10

$plugin->maturity = MATURITY_STABLE;$plugin->maturity = MATURITY_STABLE;

$plugin->release = '1.4 (2025-09-25) - Đợt mở môn đơn giản với môn học từ Moodle';$plugin->release = '1.3 (2025-09-25) - Cấu trúc đơn giản: đợt học và môn học';