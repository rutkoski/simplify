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
 * Callback filter.
 *
 * @author Rodrigo Rutkoski Rodrigues, <rutkoski@gmail.com>
 * @package Simplify_Kernel_Data_Filter
 */
class CallbackFilter implements FilterInterface
{

  /**
   * Callback.
   *
   * @var string|array
   */
  public $callback;

  /**
   * Extra parameters for callback.
   *
   * @var array
   */
  public $extraParams;

  /**
   * Value parameter positon.
   *
   * @var integer
   */
  public $valueParamPos;

  /**
   * Constructor.
   *
   * @param string|array $callback Valid PHP callback.
   * @return void
   */
  public function __construct($callback = null, $extraParams = array(), $valueParamPos = 0)
  {
    $this->callback = $callback;
    $this->extraParams = $extraParams;
    $this->valueParamPos = $valueParamPos;
  }

  /**
   * (non-PHPdoc)
   * @see simplify/kernel/data/filter/IFilter#filter($value)
   */
  public function filter($value)
  {
    $args = $this->extraParams;
    array_splice($args, $this->valueParamPos, 0, $value);
    return call_user_func_array($this->callback, $args);
  }

}

?>