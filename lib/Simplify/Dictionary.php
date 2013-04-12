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
 * Basic Simplify_DictionaryInterface implementation.
 *
 */
class Simplify_Dictionary implements Simplify_DictionaryInterface, ArrayAccess
{

  /**
   * Filter null values
   */
  const FILTER_NONE = 0;

  /**
   * Filter null values
   */
  const FILTER_NULL = 1;

  /**
   * Filter empty values
   */
  const FILTER_EMPTY = 2;

  /**
   * Holds Simplify_Dictionary name/value pairs.
   *
   * @var array
   */
  protected $data = array();

  /**
   * Constructor.
   *
   * @param mixed $data initial data to populate the Simplify_Dictionary
   * @return void
   */
  public function __construct($data = null)
  {
    $this->reset($data);
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_DictionaryInterface::get()
   */
  public function __get($name)
  {
    return $this->get($name);
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_DictionaryInterface::set()
   */
  public function __set($name, $value)
  {
    return $this->set($name, $value);
  }

  /**
   * (non-PHPdoc)
   * @see ArrayAccess::offsetSet()
   */
  public function offsetSet($offset, $value)
  {
    $this->set($offset, $value);
  }

  /**
   * (non-PHPdoc)
   * @see ArrayAccess::offsetExists()
   */
  public function offsetExists($offset)
  {
    return $this->has($offset);
  }

  /**
   * (non-PHPdoc)
   * @see ArrayAccess::offsetUnset()
   */
  public function offsetUnset($offset)
  {
    $this->del($offset);
  }

  /**
   * (non-PHPdoc)
   * @see ArrayAccess::offsetGet()
   */
  public function offsetGet($offset)
  {
    return $this->get($offset);
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_DictionaryInterface::copyAll()
   */
  public function copyAll($data, $flags = 0)
  {
    return $this->_copyAll($data, $flags);
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_DictionaryInterface::del()
   */
  public function del($name)
  {
    return $this->_del($name);
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_DictionaryInterface::get()
   */
  public function get($name, $default = null, $flags = 0)
  {
    $args = func_get_args();

    $func = array($this, '_get');

    if ($this->hasGetter($name)) {
      $func = array($this, 'get_' . $name);
      unset($args[0]);
    }

    return call_user_func_array($func, $args);
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_DictionaryInterface::getAll()
   */
  public function getAll($flags = 0)
  {
    $data = $this->data;

    foreach ($this->getGetterNames() as $name) {
      $data[$name] = $this->{$name};
    }

    return $data;
  }

  /**
   * Get the setter names
   *
   * @return string[]
   */
  public function getSetterNames()
  {
    $names = array();

    $methods = get_class_methods($this);

    foreach ($methods as $method) {
      if (strpos($method, 'set_') === 0) {
        $names[] = substr($method, 4);
      }
    }

    return $names;
  }

  /**
   * Get the getter names
   *
   * @return string[]
   */
  public function getGetterNames()
  {
    $names = array();

    $methods = get_class_methods($this);

    foreach ($methods as $method) {
      if (strpos($method, 'get_') === 0) {
        $names[] = substr($method, 4);
      }
    }

    return $names;
  }

  /**
   * Check if getter exists
   *
   * @param string $name getter name
   * @return boolean
   */
  public function hasGetter($name)
  {
    return method_exists($this, 'get_' . $name);
  }

  /**
   * Check if setter exists
   *
   * @param string $name setter name
   * @return boolean
   */
  public function hasSetter($name)
  {
    return method_exists($this, 'set_' . $name);
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_DictionaryInterface::getNames()
   */
  public function getNames()
  {
    $names = array_keys($this->data);
    return $names;
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_DictionaryInterface::has()
   */
  public function has($name, $flags = 0)
  {
    return $this->_has($name);
  }

  /**
   * Create Simplify_Dictionary from $data
   *
   * If $data is already a Simplify_Dictionary, it is returned unchanged.
   *
   * @param mixed $data
   * @return Simplify_Dictionary
   */
  public static function parseFrom($data)
  {
    if (!($data instanceof Simplify_DictionaryInterface)) {
      $data = new Simplify_Dictionary($data);
    }

    return $data;
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_DictionaryInterface::reset()
   */
  public function reset($data = null)
  {
    $this->data = array();
    return $this->copyAll($data);
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_DictionaryInterface::set()
   */
  public function set($name, $value)
  {
    $args = func_get_args();

    $func = array($this, '_set');

    if ($this->hasSetter($name)) {
      $func = array($this, 'set_' . $name);
      unset($args[0]);
    }

    return call_user_func_array($func, $args);
  }

  /**
   * Get a reference to the internal array.
   *
   * @return array
   */
  public function &getData()
  {
    return $this->data;
  }

  /**
   * Set the internal array by reference.
   *
   * @param mixed $data
   * @return SimpleSimplify_Dictionary
   */
  public function setData(&$data)
  {
    $this->data = & $data;
    return $this;
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_DictionaryInterface::copyAll()
   */
  protected function _copyAll($data, $flags = 0)
  {
    if (empty($data))
      return;

    if ($data instanceof Simplify_DictionaryInterface) {
      $data = $data->getAll();
    }

    foreach ($data as $name => $value) {
      if ((Simplify_Dictionary::FILTER_NULL & $flags) == $flags && is_null($value))
        continue;

      if ((Simplify_Dictionary::FILTER_EMPTY & $flags) == $flags && empty($value))
        continue;

      $this->set($name, $value);
    }

    return $this;
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_DictionaryInterface::get()
   */
  protected function _del($name)
  {
    if (isset($this->data[$name])) {
      unset($this->data[$name]);
    }

    return $this;
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_DictionaryInterface::get()
   */
  protected function _get($name, $default = null, $flags = 0)
  {
    if ($this->_has($name, $flags)) {
      return $this->data[$name];
    }

    return $default;
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_DictionaryInterface::has()
   */
  protected function _has($name, $flags = 0)
  {
    if (!isset($this->data[$name])) {
      return false;
    }

    if ((Simplify_Dictionary::FILTER_NULL & $flags) == $flags && is_null($this->data[$name])) {
      return false;
    }

    if ((Simplify_Dictionary::FILTER_EMPTY & $flags) == $flags && empty($this->data[$name])) {
      return false;
    }

    return true;
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_DictionaryInterface::set()
   */
  protected function _set($name, $value)
  {
    $this->data[$name] = $value;
    return $this;
  }

}
