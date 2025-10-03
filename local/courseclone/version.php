<?php
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
 * Version information for Course Clone RESTful API plugin.
 *
 * @package    local_courseclone
 * @copyright  2025 Course Clone Team
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version   = 2025100301;        // Plugin version (YYYYMMDDXX).
$plugin->requires  = 2019111800;        // Requires Moodle 3.8+.
$plugin->component = 'local_courseclone'; // Plugin component name.
$plugin->maturity  = MATURITY_STABLE;
$plugin->release   = '1.0.0';