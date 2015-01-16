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
 * @author Rodrigo Rutkoski Rodrigues <rutkoski@gmail.com>
 */

namespace Simplify;

/**
 *
 * Facade for cache
 *
 */
class Cache
{

  /**
   * @var CacheInterface[]
   */
  protected static $handlers;

  /**
   *
   * @param unknown_type $id
   * @return boolean
   */
  public static function cached($id)
  {
    return self::getHandler()->cached($id);
  }

  public static function delete($id)
  {
    return self::getHandler()->delete($id);
  }

  /**
   *
   * @param string $id
   * @return mixed
   */
  public static function read($id)
  {
    return self::getHandler()->read($id);
  }

  public static function flush()
  {
    return self::getHandler()->flush();
  }

  public static function write($id, $data = '', $ttl = null)
  {
    return self::getHandler()->write($id, $data, $ttl);
  }

  public static function setHandler($type, CacheInterface $handler)
  {
    self::$handler[$type] = $handler;
  }

  /**
   * Get the cache implementation
   *
   * @param string $class
   * @return CacheInterface
   */
  public static function getHandler($class = null)
  {
    if (!$class)
      $class = 'Simplify\Cache\File';

    if (empty(self::$handlers[$class])) {
      self::$handlers[$class] = new $class;
    }

    return self::$handlers[$class];
  }

}
