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
 * PHPUnit tests for Hello World block
 *
 * @package    block_helloworld
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class block_helloworld_testcase extends advanced_testcase {

    public function test_block_creation() {
        $this->resetAfterTest();
        
        // Create a course
        $course = $this->getDataGenerator()->create_course();
        
        // Test that the block can be instantiated
        $block = new block_helloworld();
        $this->assertInstanceOf('block_helloworld', $block);
        
        // Test initialization
        $block->init();
        $this->assertNotEmpty($block->title);
    }
    
    public function test_block_content() {
        global $USER, $COURSE;
        $this->resetAfterTest();
        
        // Create a test user and course
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $this->setUser($user);
        $COURSE = $course;
        
        // Create block instance
        $block = new block_helloworld();
        $block->init();
        
        // Test content generation
        $content = $block->get_content();
        $this->assertNotNull($content);
        $this->assertNotEmpty($content->text);
    }
}