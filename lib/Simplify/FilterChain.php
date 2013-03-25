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

/**
 * 
 * Manages chains of filters
 * 
 * Filters as callbacks that receive a value, process and return the filtered value
 *
 */
class Simplify_FilterChain
{

  protected $filters = array();

  /**
   * Add a filter to a chain
   * 
   * @param string $chain the chain name
   * @param callback $callback the callback
   * @return Simplify_FilterChain
   */
  public function add($chain, $callback)
  {
    $args = func_get_args();
    unset($args[0], $args[1]);
    
    $this->filters[$chain][] = array($callback, $args);
    return $this;
  }

  /**
   * Call a chain
   * 
   * @param string $chain the chain name
   * @param mixed $output the value to be filtered
   * @return mixed
   */
  public function call($chain, $output)
  {
    if (empty($this->filters[$chain])) {
      return $output;
    }
    
    $args = func_get_args();
    unset($args[0], $args[1]);
    
    $filters = $this->filters[$chain];
    
    foreach ($filters as $filter) {
      $callback = $filter[0];
      $params = $filter[1];
      
      $params = array($output) + $params + $args;
      
      $output = call_user_func_array($callback, $params);
    }
    
    return $output;
  }

}
