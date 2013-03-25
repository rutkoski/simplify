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
 * Default implementation of Simplify_Data_HolderInterface
 *
 */
class Simplify_Data_Holder extends Simplify_Dictionary implements Simplify_Data_HolderInterface
{

  /**
   *
   * @var boolean
   */
  protected $dirty = false;

  /**
   * Store modified data
   *
   * @var array
   */
  protected $modified = array();

  /**
   * (non-PHPdoc)
   * @see DataHolderInterface::commit()
   */
  public function commit($names = null)
  {
    if (empty($names)) {
      $this->data = array_merge($this->data, $this->modified);
      $this->modified = array();
      $this->dirty = false;
    }
    
    else {
      $names = (array) $names;
      
      foreach ($names as $name) {
        if (isset($this->modified[$name])) {
          $this->data[$name] = $this->modified[$name];
        }
      }
    }
    
    return $this;
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Dictionary::getAll()
   */
  public function getAll($flags = 0)
  {
    return array_merge(parent::getAll(), $this->modified);
  }

  /**
   * (non-PHPdoc)
   * @see DataHolderInterface::getModified()
   */
  public function getModified()
  {
    return $this->modified;
  }

  /**
   * (non-PHPdoc)
   * @see SimpleSimplify_Dictionary::getNames()
   */
  public function getNames()
  {
    return array_merge(parent::getNames(), array_keys($this->modified));
  }

  /**
   * (non-PHPdoc)
   * @see DataHolderInterface::isDirty()
   */
  public function isDirty()
  {
    return $this->dirty;
  }

  /**
   * (non-PHPdoc)
   * @see SimpleSimplify_Dictionary::reset()
   */
  public function reset($data = null)
  {
    $this->modified = array();
    return parent::reset($data);
  }

  /**
   * (non-PHPdoc)
   * @see SimpleSimplify_Dictionary::_del()
   */
  protected function _del($name)
  {
    if (isset($this->modified[$name])) {
      unset($this->modified[$name]);
    }
    
    $this->dirty = (boolean) count($this->modified);
    
    return parent::_del($name);
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Dictionary::_get()
   */
  protected function _get($name, $default = null, $flags = 0)
  {
    if (isset($this->modified[$name])) {
      return $this->modified[$name];
    }
    
    return parent::_get($name, $default, $flags);
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Dictionary::_has()
   */
  protected function _has($name)
  {
    return parent::_has($name) || isset($this->modified[$name]);
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Dictionary::_set()
   */
  protected function _set($name, $value)
  {
    if (parent::_get($name) === $value) {
      unset($this->modified[$name]);
    }
    else {
      $this->modified[$name] = $value;
    }
    
    $this->dirty = (boolean) count($this->modified);
    
    return $this;
  }

}
