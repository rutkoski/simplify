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
 * Simplify_Data_View profides a Simplify_Dictionary interface to any array or array-like object
 *
 */
class Simplify_Data_View extends Simplify_Dictionary
{

  /**
   * 
   * @param mixed $data
   */
  public function __construct(& $data = null)
  {
    parent::__construct();

    if (! is_null($data)) {
      $this->setData($data);
    }
  }

  public static function getIterator($data)
  {
    $class = get_class($this);
    return new Simplify_Data_ViewIterator($data, $class);
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Dictionary::_del()
   */
  protected function _del($name)
  {
    if (is_array($this->data)) {
      if (isset($this->data[$name])) {
        unset($this->data[$name]);
      }
    }
    elseif ($this->data instanceof Simplify_DictionaryInterface) {
      $this->data->del($name);
    }

    return $this;
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Dictionary::_get()
   */
  protected function _get($name, $default = null)
  {
    if (is_array($this->data)) {
      if (isset($this->data[$name])) {
        return $this->data[$name];
      }
    }
    elseif ($this->data instanceof Simplify_DictionaryInterface) {
      $args = func_get_args();
      return call_user_func_array(array($this->data, 'get'), $args);
    }

    return $default;
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Dictionary::_has()
   */
  protected function _has($name)
  {
    if (is_array($this->data)) {
      return isset($this->data[$name]);
    }
    elseif ($this->data instanceof Simplify_DictionaryInterface) {
      return $this->data->has($name);
    }
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Dictionary::_set()
   */
  protected function _set($name, $value)
  {
    if (is_array($this->data)) {
      $this->data[$name] = $value;
    }
    elseif ($this->data instanceof Simplify_DictionaryInterface) {
      $this->data->set($name, $value);
    }

    return $this;
  }

}
