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

namespace Simplify\Cache;

use Simplify;
use Simplify\CacheInterface;
use Simplify\CacheException;

/**
 * File Cache.
 *
 * @author Rodrigo Rutkoski Rodrigues <rutkoski@gmail.com>
 * @package Kernel_Cache
 */
class File implements CacheInterface
{

  /**
   * @var string cache base path
   */
  public $path;

  /**
   * @var int cache time to live
   */
  public $ttl;

  /**
   * Constructor.
   *
   * @param string $path base path
   * @param string $extension cached file extension
   * @return void
   */
  public function __construct($path = null, $ttl = null)
  {
    if (empty($path)) {
      $path = Simplify::config()->get('cache_dir', '{app:dir}/cache');
    }

    $path = sy_fix_path($path);

    if (!is_dir($path) && !mkdir($path)) {
      throw new CacheException("Cache dir does not exist and could not be created: <b>$path</b>");
    }

    if (!is_writable($path) && !chmod($path, 0755)) {
      throw new CacheException("Cache dir is not writable by the web server: <b>$path</b>");
    }

    $this->path = $path;
    $this->ttl = $ttl;
  }

  /**
   * (non-PHPdoc)
   * @see CacheInterface::cached()
   */
  public function cached($id)
  {
    $file = $this->findFile($id, $this->ttl);

    if (empty($file))
      return false;

    $filename = basename($file);

    if (strpos($filename, '_') === false) {
      return true;
    }

    $ttl = substr($filename, strpos($filename, '_') + 1, strpos($filename, '.') - strlen($filename));

    return mktime() <= $ttl;
  }

  /**
   * (non-PHPdoc)
   * @see CacheInterface::delete()
   */
  public function delete($id)
  {
    $file = $this->findFile($id);

    if (!empty($file)) {
      unlink($file);
    }
  }

  /**
   * (non-PHPdoc)
   * @see CacheInterface::flush()
   */
  public function flush()
  {
    if (($handle = opendir($this->path)) !== false) {
      while (false !== ($file = readdir($handle))) {
        if ($file == "." || $file == "..")
          continue;

        unlink($this->path . '/' . $file);
      }

      closedir($handle);
    }
  }

  /**
   * (non-PHPdoc)
   * @see CacheInterface::read()
   */
  public function read($id)
  {
    $file = $this->findFile($id);

    if (empty($file)) {
      throw new CacheException('Cache file not found');
    }

    $filename = basename($file);

    if (strpos($filename, '_') !== false) {
      $ttl = substr($filename, strpos($filename, '_') + 1, strpos($filename, '.') - strlen($filename));

      if (mktime() > $ttl) {
        unlink($file);

        throw new CacheException('Cached file ttl expired');
      }
    }

    $str = file_get_contents($file);
    $uns = @unserialize($str);

    return ($str == serialize(false) || $uns !== false) ? $uns : $str;
  }

  /**
   * (non-PHPdoc)
   * @see CacheInterface::write()
   */
  public function write($id, $data = '', $ttl = null)
  {
    if (is_null($ttl))
      $ttl = $this->ttl;

    $file = $this->findFile($id);

    $name = $id;

    if ($ttl) {
      $ttl += mktime();

      $name .= '_' . $ttl;
    }

    if (empty($file)) {
      $file = sy_fix_path($this->path . '/' . $name, 'php');
    } else {
      rename($file, $this->path . '/' . $name . '.php');

      $file = $this->path . '/' . $name . '.php';
    }

    if (! is_string($data)) {
      $data = serialize($data);
    }

    file_put_contents($file, $data);
  }

  /**
   * Find the cache filename
   *
   * @param string $id cache name
   * @return string cache filename
   */
  protected function findFile($id)
  {
    $found = glob($this->path . '/' . $id . '*.php');
    return sy_get_param((array)$found, 0);
  }

}
