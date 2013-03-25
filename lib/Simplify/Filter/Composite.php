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
 * Composite filter
 *
 */
class Simplify_Filter_Composite implements Simplify_FilterInterface
{

  /**
   * Filters
   *
   * @var array
   */
  public $filters = array();

  /**
   * (non-PHPdoc)
   * @see Simplify_FilterInterface::filter()
   */
  public function filter($value)
  {
    foreach ($this->filters as $filter) {
      $value = $filter->filter($value);
    }
    
    return $value;
  }

  /**
   * Add filter
   *
   * @param Simplify_FilterInterface $filter
   * @return Simplify_Filter_Composite
   */
  public function addFilter(IFilter $filter)
  {
    $this->filters[] = $filter;
    return $this;
  }

}
