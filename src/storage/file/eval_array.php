<?php
/**
 * File containing the ezcCacheStorageEvalarray class.
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
 * @version //autogentag//
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @filesource
 */

/**
 * This cache storage implementation stores arrays and scalar values (int,
 * float, string, bool) in files on your hard disk as JSON.
 *
 * This class replaces the previous eval()-based implementation. The eval()
 * approach allowed arbitrary PHP code execution if an attacker could write
 * to the cache directory. JSON serialization carries no code execution risk.
 *
 * Note: This change is not backwards compatible with existing cache files
 * written by the eval()-based implementation. Clear your cache directory
 * when upgrading.
 *
 * @package Cache
 * @version //autogentag//
 */
class ezcCacheStorageFileEvalArray extends ezcCacheStorageFile
{
    /**
     * Fetch data from a given file name.
     *
     * @see ezcCacheStorageFile::restore()
     *
     * @param string $filename The file to fetch data from.
     * @return mixed The data read from the file.
     *
     * @throws ezcCacheInvalidDataException if JSON decoding fails.
     */
    protected function fetchData( $filename )
    {
        $raw = file_get_contents( $filename );
        $data = json_decode( $raw, true );
        if ( $data === null && $raw !== 'null' )
        {
            throw new ezcCacheInvalidDataException(
                $filename,
                array( 'valid JSON' )
            );
        }
        return $data;
    }

    /**
     * Serialize the data for storing as JSON.
     *
     * @param mixed $data Simple type or array to serialize.
     * @return string The serialized data.
     *
     * @throws ezcCacheInvalidDataException
     *         If the data submitted is an object or a resource.
     */
    protected function prepareData( $data )
    {
        if ( is_object( $data ) || is_resource( $data ) )
        {
            throw new ezcCacheInvalidDataException( gettype( $data ), array( 'simple', 'array' ) );
        }
        $encoded = json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
        if ( $encoded === false )
        {
            throw new ezcCacheInvalidDataException( gettype( $data ), array( 'JSON-encodable' ) );
        }
        return $encoded;
    }
}
