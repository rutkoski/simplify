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
class StringFilter implements FilterInterface
{

  const STRTOLOWER = 'strtolower';

  const STRTOUPPER = 'strtoupper';

  const TRIM = 'trim';

  /**
   *
   * @var string
   */
  public $filter;

  /**
   * Constructor.
   *
   * @param string|array $callback Valid PHP callback.
   * @return void
   */
  public function __construct($filter = null)
  {
    $this->filter = $filter;
  }

  /**
   * (non-PHPdoc)
   * @see simplify/kernel/data/filter/IFilter#filter($value)
   */
  public function filter($value)
  {
    if (! is_string($value)) {
      return $value;
    }

    switch ($this->filter) {
      default:
        return call_user_func($this->filter, $value);
    }
  }

}
