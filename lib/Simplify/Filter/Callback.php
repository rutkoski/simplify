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
 * @copyright Copyright 2008 Rodrigo Rutkoski Rodrigues
 */

/**
 * 
 * Callback filter
 *
 */
class Simplify_Filter_Callback implements Simplify_FilterInterface
{

  /**
   * Callback
   *
   * @var callback
   */
  public $callback;

  /**
   * Extra parameters for callback
   *
   * @var mixed[]
   */
  public $extraParams;

  /**
   * Value parameter positon
   *
   * @var integer
   */
  public $valueParamPos;

  /**
   * 
   * @param callback $callback
   * @param array $extraParams extra callback parameters
   * @param int $valueParamPos value parameter position
   */
  public function __construct($callback = null, array $extraParams = array(), $valueParamPos = 0)
  {
    $this->callback = $callback;
    $this->extraParams = $extraParams;
    $this->valueParamPos = $valueParamPos;
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_FilterInterface::filter()
   */
  public function filter($value)
  {
    $args = $this->extraParams;
    array_splice($args, $this->valueParamPos, 0, $value);
    return call_user_func_array($this->callback, $args);
  }

}
