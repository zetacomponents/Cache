<?php
/**
 * ezcCacheStackLfuMetaData 
 * 
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements.  See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership.  The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License.  You may obtain a copy of the License at
 * 
 *   http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied.  See the License for the
 * specific language governing permissions and limitations
 * under the License.
 *
 * @package Cache
 * @subpackage Tests
 * @version //autogen//
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */

require_once 'base_meta_data_test.php';

/**
 * Test suite for the ezcCacheStackLfuMetaData class.
 * 
 * @package Cache
 * @subpackage Tests
 */
class ezcCacheStackLfuMetaDataTest extends ezcCacheStackBaseMetaDataTest
{
    public static function suite()
	{
		return new PHPUnit_Framework_TestSuite( __CLASS__ );
	}

    public function setup()
    {
        $this->metaDataClass = 'ezcCacheStackLfuMetaData';
    }

    public function testAddItem()
    {
        $meta = new $this->metaDataClass();

        $this->assertAttributeEquals(
            array(),
            'replacementData',
            $meta
        );

        // Add first item to unknown storage
        $meta->addItem( 'storage_id_1', 'item_id_1' );

        $metaData = $meta->getState();
        $this->assertEquals(
            1,
            $metaData['replacementData']['item_id_1']
        );

        // Add first item to second unknown storage
        $meta->addItem( 'storage_id_2', 'item_id_1' );

        $metaData = $meta->getState();
        $this->assertEquals(
            2,
            $metaData['replacementData']['item_id_1']
        );

        // Add second item to known storag
        $meta->addItem( 'storage_id_2', 'item_id_2' );

        $metaData = $meta->getState();
        $this->assertEquals(
            1,
            $metaData['replacementData']['item_id_2']
        );
        $this->assertEquals(
            2,
            $metaData['replacementData']['item_id_1']
        );

        // Add existing item
        $meta->addItem( 'storage_id_1', 'item_id_1' );

        $metaData = $meta->getState();
        $this->assertEquals(
            1,
            $metaData['replacementData']['item_id_2']
        );
        $this->assertEquals(
            3,
            $metaData['replacementData']['item_id_1']
        );
    }
}

?>
