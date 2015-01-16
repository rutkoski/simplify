<?php

/**
 * SimplifyPHP Framework
 *
 * This file is part of SimplifyPHP Framework.
 *
 * SimplifyPHP Framework is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * SimplifyPHP Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Rodrigo Rutkoski Rodrigues, <rutkoski@gmail.com>
 */

namespace Simplify;

/**
 *
 * Interface for cache
 *
 */
interface CacheInterface
{

  /**
   * Check if item is cached and not timedout.
   *
   * @param $id string Cached item identifier. Should be unique.
   * @return boolean True if cached and not timedout, false otherwise.
   */
  public function cached($id);

  /**
   * Delete cached item.
   *
   * @param $id string Cached item identifier. Should be unique.
   * @return boolean True if cached and not timedout, false otherwise.
   */
  public function delete($id);

  /**
   * Read data from cache.
   *
   * @param $id string Cached item identifier. Should be unique.
   * @throws CacheException if item not cached or timedout.
   * @return mixed
   */
  public function read($id);

  /**
   * Flush cache.
   *
   * @return void
   */
  public function flush();

  /**
   * Write data to cache.
   *
   * @param $id string Cached item identifier. Should be unique.
   * @param $data string Cache data.
   * @param $timeout int Cache timeout, in seconds.
   * @return void
   */
  public function write($id, $value = '', $ttl = null);

}
