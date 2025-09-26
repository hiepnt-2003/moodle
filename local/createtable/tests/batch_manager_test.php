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
 * Unit tests for batch_manager class.
 *
 * @package    local_createtable
 * @copyright  2025 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \local_createtable\batch_manager
 */

namespace local_createtable;

/**
 * Unit tests for the batch_manager class.
 *
 * @package    local_createtable
 * @copyright  2025 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \local_createtable\batch_manager
 */
class batch_manager_test extends \advanced_testcase {

    /**
     * Test batch creation.
     *
     * @covers ::create_batch
     */
    public function test_create_batch() {
        $this->resetAfterTest();

        $name = 'Test Batch';
        $opendate = time();

        $batchid = batch_manager::create_batch($name, $opendate);

        $this->assertNotEmpty($batchid);
        $this->assertIsInt($batchid);

        // Verify batch was created.
        $batch = batch_manager::get_batch($batchid);
        $this->assertNotFalse($batch);
        $this->assertEquals($name, $batch->name);
        $this->assertEquals($opendate, $batch->open_date);
    }

    /**
     * Test batch retrieval.
     *
     * @covers ::get_batch
     */
    public function test_get_batch() {
        $this->resetAfterTest();

        $name = 'Test Batch';
        $opendate = time();
        $batchid = batch_manager::create_batch($name, $opendate);

        $batch = batch_manager::get_batch($batchid);

        $this->assertNotFalse($batch);
        $this->assertEquals($batchid, $batch->id);
        $this->assertEquals($name, $batch->name);
        $this->assertEquals($opendate, $batch->open_date);
    }

    /**
     * Test batch deletion.
     *
     * @covers ::delete_batch
     */
    public function test_delete_batch() {
        $this->resetAfterTest();

        $name = 'Test Batch';
        $opendate = time();
        $batchid = batch_manager::create_batch($name, $opendate);

        $result = batch_manager::delete_batch($batchid);

        $this->assertTrue($result);

        // Verify batch was deleted.
        $batch = batch_manager::get_batch($batchid);
        $this->assertFalse($batch);
    }
}