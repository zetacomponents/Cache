<?php
/**
 * File containing the ezcCacheStorageFileJsonArray class.
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
 * float, string, bool, null) in files on your hard disk as JSON. In contrast
 * to its sibling classes {@link ezcCacheStorageFileArray} and
 * {@link ezcCacheStorageFileEvalArray}, the stored data is not PHP code: it is
 * restored with json_decode(), so a cache file is never executed. It takes its
 * base methods from the extended storage base class {@link ezcCacheStorageFile}.
 *
 * Strings must be valid UTF-8, and floats must be finite, since JSON can not
 * represent anything else. Arrays are restored as PHP arrays; string keys that
 * look like integers come back as integer keys, as they do in any PHP array.
 *
 * For example code of using a cache storage, see {@link ezcCacheManager}.
 *
 * The Cache package contains several other implementations of
 * {@link ezcCacheStorageFile}. As there are:
 *
 * - ezcCacheStorageFileArray
 * - ezcCacheStorageFileEvalArray
 * - ezcCacheStorageFilePlain
 *
 * @package Cache
 * @version //autogentag//
 */
class ezcCacheStorageFileJsonArray extends ezcCacheStorageFile
{
    /**
     * Fetch data from a given file name.
     *
     * @see ezcCacheStorageFile::restore()
     *
     * @param string $filename The file to fetch data from.
     * @return mixed The data read from the file, or false if the file can not
     *               be read or does not contain valid JSON.
     */
    protected function fetchData( $filename )
    {
        $json = file_get_contents( $filename );
        if ( $json === false )
        {
            return false;
        }
        $data = json_decode( $json, true );
        if ( json_last_error() !== JSON_ERROR_NONE )
        {
            return false;
        }
        return $data;
    }

    /**
     * Serialize the data for storing.
     * Serializes a PHP variable (except type resource and object, also inside
     * arrays) to a JSON string.
     *
     * @param mixed $data Simple type or array to serialize.
     * @return string The serialized data
     *
     * @throws ezcCacheInvalidDataException
     *         If the data submitted is or contains an object or a resource,
     *         since this implementation of {@link ezcCacheStorageFile} can
     *         only deal with scalar and array values, or if it can not be
     *         represented as JSON (a string that is not valid UTF-8, a float
     *         that is not finite).
     */
    protected function prepareData( $data )
    {
        if ( is_object( $data ) || is_resource( $data ) )
        {
            throw new ezcCacheInvalidDataException( gettype( $data ), array( 'simple', 'array' ) );
        }
        if ( is_array( $data ) )
        {
            array_walk_recursive( $data, function ( $value )
            {
                if ( is_object( $value ) || is_resource( $value ) )
                {
                    throw new ezcCacheInvalidDataException( gettype( $value ), array( 'simple', 'array' ) );
                }
            } );
        }
        $json = json_encode( $data, JSON_PRESERVE_ZERO_FRACTION );
        if ( $json === false )
        {
            throw new ezcCacheInvalidDataException( gettype( $data ), array( 'UTF-8 string', 'finite float', 'int', 'bool', 'null', 'array' ) );
        }
        return $json;
    }
}
?>
