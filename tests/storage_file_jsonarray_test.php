<?php
/**
 * ezcCacheStorageFileJsonArrayTest
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

/**
 * Require parent test class.
 */
require_once 'storage_test.php';

/**
 * Test suite for ezcStorageFileJsonArray class.
 *
 * @package Cache
 * @subpackage Tests
 */
class ezcCacheStorageFileJsonArrayTest extends ezcCacheStorageTest
{
    public function testStoredDataIsJson()
    {
        $data = array( 'a' => 1, 'b' => array( 1.0, 'two', true, null ) );
        $this->storage->store( 'json', $data );

        $files = glob( $this->storage->getLocation() . '*' );
        $this->assertCount( 1, $files );
        $this->assertSame( $data, json_decode( file_get_contents( $files[0] ), true ) );
        $this->assertSame( $data, $this->storage->restore( 'json' ) );
    }

    public function testFloatsAndNullKeepTheirType()
    {
        $this->storage->store( 'float', array( 1.0, 0.5, null ) );
        $this->assertSame( array( 1.0, 0.5, null ), $this->storage->restore( 'float' ) );
        $this->storage->store( 'scalar', 2.0 );
        $this->assertSame( 2.0, $this->storage->restore( 'scalar' ) );
    }

    public function testPhpCodeInACacheFileIsNotExecuted()
    {
        $this->storage->store( 'code', array( 'a' ) );
        $files = glob( $this->storage->getLocation() . '*' );
        $marker = $this->getTempDir() . '/executed';
        file_put_contents( $files[0], "return touch( " . var_export( $marker, true ) . " );\n?>\n" );

        $this->assertFalse( $this->storage->restore( 'code' ) );
        $this->assertFileDoesNotExist( $marker );
    }

    public function testObjectInsideAnArrayIsRejected()
    {
        try
        {
            $this->storage->store( 'object', array( 'a', array( new stdClass ) ) );
            $this->fail( 'Expected exception not thrown.' );
        }
        catch ( ezcCacheInvalidDataException $e )
        {
            $this->assertStringContainsString( "'object'", $e->getMessage() );
        }
        $this->assertEquals( 0, $this->storage->countDataItems( 'object' ) );
    }

    public function testInvalidUtf8IsRejected()
    {
        try
        {
            $this->storage->store( 'binary', array( "\xff\xfe" ) );
            $this->fail( 'Expected exception not thrown.' );
        }
        catch ( ezcCacheInvalidDataException $e )
        {
            $this->assertStringContainsString( 'UTF-8 string', $e->getMessage() );
        }
        $this->assertEquals( 0, $this->storage->countDataItems( 'binary' ) );
    }

    public static function suite()
    {
        return new PHPUnit\Framework\TestSuite( "ezcCacheStorageFileJsonArrayTest" );
    }
}
?>
