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
 * Generic composite filter.
 *
 * @author Rodrigo Rutkoski Rodrigues, <rutkoski@gmail.com>
 * @package Simplify_Kernel_Data_Filter
 */
class CompositeFilter implements FilterInterface
{

  /**
   * Filters.
   *
   * @var array
   */
  public $filters = array();

  /**
   * (non-PHPdoc)
   * @see simplify/kernel/data/filter/IFilter#filter($value)
   */
  public function filter($value)
  {
    foreach ($this->filters as $filter) {
      $value = $filter->filter($value);
    }

    return $value;
  }

  /**
   * Add filter.
   *
   * @param IFilter $filter
   * @return IFilter
   */
  public function addFilter(IFilter $filter)
  {
    $this->filters[] = $filter;
    return $filter;
  }

  /**
   * Add filter at specified index.
   *
   * @param IFilter $filter
   * @param integer $index
   * @return IFilter
   */
  public function addFilterAt(IFilter $filter, $index)
  {
    array_splice($this->filters, $index, 0, $filter);
    return $filter;
  }

  /**
   * Get filter at specified index.
   *
   * @param IFilter $index
   * @return IFilter
   */
  public function getFilterAt(IFilter $index)
  {
    return $this->filters[$index];
  }

  /**
   * Get index for specified filter.
   *
   * @param IFilter $filter
   * @return integer
   */
  public function getFilterIndex(IFilter $filter)
  {
    $i = 0;

    while ($i < count($this->filters) && $this->filters[$i] !== $filter)
      $i ++;

    if ($i >= count($this->filters)) throw new Exception('Filter not found');

    return $i;
  }

  /**
   * Remove filter.
   *
   * @param IFilter $filter
   * @return IFilter
   */
  public function removeFilter(IFilter $filter)
  {
    $this->removeFilterAt($this->getFilterIndex($filter));
    return $filter;
  }

  /**
   * Remove filter at specified index.
   *
   * @param integer $index
   * @return IFilter
   */
  public function removeFilterAt($index)
  {
    $filter = $this->filters[$index];
    array_splice($this->filters, $index, 1);
    return $filter;
  }

}

?>