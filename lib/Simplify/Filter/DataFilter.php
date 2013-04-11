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
 * Filter a data set
 *
 */
class Simplify_Filter_DataFilter
{

  const FILTER_ALL = '*';

  /**
   * Filters
   *
   * @var Simplify_FilterInterface[]
   */
  protected $filters = array();

  /**
   * Constructor
   *
   * @param Simplify_FilterInterface[] $filters
   */
  public function __construct(array $filters = null)
  {
    if (is_array($filters)) {
      $this->parse($filters);
    }
  }

  /**
   *
   * @return Simplify_Filter_DataFilter
   */
  public static function parseFrom($filter)
  {
    if (! ($filter instanceof Simplify_Filter_DataFilter)) {
      $filter = new self($filter);
    }

    return $filter;
  }

  /**
   * Apply filters to data set or a given key in data set
   *
   * @param mixed $data data set
   * @param string $name key in data set
   */
  public function applyFilters(&$data, $name = null)
  {
    if (empty($name)) {
      foreach ($data as $name => $value) {
        $data = $this->applyFilters($data, $name);
      }
    }
    else {
      if (isset($this->filters[self::FILTER_ALL])) {
        foreach ($this->filters[self::FILTER_ALL] as $filter) {
          $data[$name] = $filter->filter($data[$name]);
        }
      }

      if (isset($this->filters[$name])) {
        foreach ($this->filters[$name] as $filter) {
          $data[$name] = $filter->filter($data[$name]);
        }
      }
    }
  }

  /**
   *
   * @param string $name
   * @param Simplify_FilterInterface $filter
   * @return Simplify_Filter_DataFilter
   */
  public function setFilter($name, Simplify_FilterInterface $filter)
  {
    if (! isset($this->filters[$name])) {
      $this->filters[$name] = array();
    }

    $this->filters[$name][] = $filter;

    return $this;
  }

  /**
   *
   * $filters = array(
   *   '*' => 'someFilter',
   *   'name' => array(
   *     'TrimFilter',
   *     'substr' => array('start' => 0, 'length' => 10)
   *   )
   * );
   *
   */
  protected function parse(array $filters)
  {
    foreach ($filters as $name => $filter) {
      if (empty($filter)) continue;

      if (is_string($filter)) {
        $Filter = $this->factory($filter);
      }

      elseif (is_array($filter)) {
        foreach ($filter as $_name => $_filter) {
          if (is_string($_filter)) {
            $Filter = $this->factory($_filter);
          }

          elseif (is_array($_filter)) {
            $Filter = $this->factory($_name, $_filter);
          }
        }
      }

      elseif ($filter instanceof Simplify_FilterInterface) {
        $Filter = $filter;
      }

      else {
        throw new Exception('Could not parse filters');
      }

      $this->setFilter($name, $Filter);
    }
  }

  /**
   * Factory a filter
   *
   * @return Simplify_FilterInterface
   */
  protected function factory($class, array $params = null)
  {
    $filter = new $class();

    foreach ($params as $param => $value) {
      $filter->{$param} = $value;
    }

    return $filter;
  }

}
