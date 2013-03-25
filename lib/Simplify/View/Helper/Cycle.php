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
 * @copyright Copyright 2008 Rodrigo Rutkoski Rodrigues
 */

/**
 *
 * Cycle a given set of values
 *
 */
class Simplify_View_Helper_Cycle extends Simplify_View_Helper
{

  /**
   *
   */
  protected static $cycles = array();

  /**
   *
   */
  protected static $every = array();

  /**
   *
   */
  public function every($id = 'default', $count = 0, $value = null, $other = null)
  {
    if (!isset(self::$every[$id])) {
      self::$every[$id] = array('count' => $count, 'value' => $value, 'other' => $other, 'current' => 0);
    }
    
    $every = & self::$every[$id];
    
    if ($every['current'] == $every['count']) {
      $output = $every['value'];
      $every['current'] = 1;
    }
    else {
      $output = $every['other'];
      $every['current'] += 1;
    }
    
    return $this->output($output);
  }

  /**
   *
   */
  public function reset($id = 'default', $values = null)
  {
    if (!isset(self::$cycles[$id])) {
      self::$cycles[$id] = array('values' => array(), 'index' => -1);
    }
    
    $args = func_get_args();
    if (count($args) > 2) {
      $values = array_slice($args, 1);
    }
    
    $cycle = self::$cycles[$id];
    
    if (!is_null($values) && $values !== $cycle['values']) {
      $cycle['values'] = $values;
    }
    
    $cycle['index'] = -1;
  }

  /**
   *
   */
  public function next($id = 'default', $values = null)
  {
    if (!isset(self::$cycles[$id])) {
      self::$cycles[$id] = array('values' => array(), 'index' => -1);
    }
    
    $args = func_get_args();
    if (count($args) > 2) {
      $values = array_slice($args, 1);
    }
    
    if (!is_null($values) && $values !== self::$cycles[$id]['values']) {
      self::$cycles[$id]['values'] = $values;
      self::$cycles[$id]['index'] = -1;
    }
    
    self::$cycles[$id]['index']++;
    if (self::$cycles[$id]['index'] >= count(self::$cycles[$id]['values'])) {
      self::$cycles[$id]['index'] = 0;
    }
    $index = self::$cycles[$id]['index'];
    
    $output = self::$cycles[$id]['values'][$index];
    
    return $this->output($output);
  }

  /**
   *
   */
  public function current($id = 'default', $values = null)
  {
    if (!isset(self::$cycles[$id])) {
      self::$cycles[$id] = array('values' => array(), 'index' => -1);
    }
    
    $args = func_get_args();
    if (count($args) > 2) {
      $values = array_slice($args, 1);
    }
    
    $cycle = self::$cycles[$id];
    
    if (!is_null($values) && $values !== $cycle['values']) {
      $cycle['values'] = $values;
      $cycle['index'] = 0;
    }
    
    $index = $cycle['index'];
    $output = $cycle['values'][$index];
    
    return $this->output($output);
  }

}
