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

/**
 * Session Cache.
 *
 * @author Rodrigo Rutkoski Rodrigues <rutkoski@gmail.com>
 * @package Kernel_Cache
 */
class Simplify_Cache_Session implements Simplify_Cache_Interface
{

  public function cached($id)
  {
    if (!isset($_SESSION['simplify_cache_session'][$id]))
      return false;

    $data = $_SESSION['simplify_cache_session'][$id];

    return mktime() <= $data['expires'];
  }

  public function delete($id)
  {
    if (isset($_SESSION['simplify_cache_session'][$id]))
      unset($_SESSION['simplify_cache_session'][$id]);
  }

  public function flush()
  {
    $_SESSION['simplify_cache_session'] = null;
  }

  public function read($id)
  {
    if (!$this->cached($id)) {
      throw new CacheException('Not cached');
    }

    $data = $_SESSION['simplify_cache_session'][$id];

    if (mktime() > $data['expires']) {
      $this->delete($id);

      throw new CacheException('Cache expired');
    }

    return $data['data'];
  }

  public function write($id, $data = '', $ttl = 0)
  {
    $_SESSION['simplify_cache_session'][$id] = array('expires' => mktime() + $ttl, 'data' => $data, );
  }

}
