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
 * YAML file Cache.
 *
 * @author Rodrigo Rutkoski Rodrigues <rutkoski@gmail.com>
 * @package Kernel_Cache
 */
class Simplify_Cache_Yaml extends Simplify_Cache_File
{

  public function __construct($path = null, $ttl = null)
  {
    parent::__construct($path, $timeout);
  }

  public function read($id)
  {
    return Syml::load(parent::read($id));
  }

  public function write($id, $data = '', $ttl = null)
  {
    return parent::write($id, Syml::dump($data), $ttl);
  }

}
