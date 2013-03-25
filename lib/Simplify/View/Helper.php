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
 * 
 * Base class for Simplify_View_Php helpers 
 *
 */
abstract class Simplify_View_Helper
{

  /**
   *
   */
  public function __get($name)
  {
    return self::factory($name);
  }

  /**
   * Factory a helper
   * 
   * @param string $name
   * @throws Exception
   * @return Simplify_View_Helper
   */
  public static function factory($name)
  {
    $helpers = s::config()->get('view:helpers');

    if (! isset($helpers[$name])) {
      throw new Exception("Helper not found: <b>{$name}</b>");
    }

    $helper = $helpers[$name];

    if (is_string($helper)) {
      $class = $helper;
    } else {
      if (isset($helper['require'])) {
        require_once($helper['require']);
      }

      $class = $helper['class'];
    }

    $Helper = new $class($helper);

    return $Helper;
  }

  /**
   *
   * @param $output
   * @return string
   */
  protected function output($output)
  {
    return $output;
  }

}
