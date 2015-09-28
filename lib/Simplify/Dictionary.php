<?php

namespace Simplify;

class Dictionary implements DictionaryInterface, \ArrayAccess
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
   * Holds Dictionary name/value pairs.
   *
   * @var array
   */
  protected $data = array();

  /**
   * Constructor.
   *
   * @param mixed $data initial data to populate the Dictionary
   * @return void
   */
  public function __construct($data = null)
  {
    $this->reset($data);
  }

  public function filter($name, $filter = FILTER_DEFAULT, $options = null)
  {
      return filter_var($this->get($name), $filter, $options);
  }

  public function filterAll($definition, $addEmpty = true)
  {
      return filter_var_array($this->getAll(), $definition, $addEmpty);
  }
  
  /**
   * (non-PHPdoc)
   * @see DictionaryInterface::get()
   */
  public function __get($name)
  {
    return $this->get($name);
  }

  /**
   * (non-PHPdoc)
   * @see DictionaryInterface::set()
   */
  public function __set($name, $value)
  {
    return $this->set($name, $value);
  }

  /**
   * (non-PHPdoc)
   * @see ArrayAccesoffsetSet()
   */
  public function offsetSet($offset, $value)
  {
    $this->set($offset, $value);
  }

  /**
   * (non-PHPdoc)
   * @see ArrayAccesoffsetExists()
   */
  public function offsetExists($offset)
  {
    return $this->has($offset);
  }

  /**
   * (non-PHPdoc)
   * @see ArrayAccesoffsetUnset()
   */
  public function offsetUnset($offset)
  {
    $this->del($offset);
  }

  /**
   * (non-PHPdoc)
   * @see ArrayAccesoffsetGet()
   */
  public function offsetGet($offset)
  {
    return $this->get($offset);
  }

  /**
   * (non-PHPdoc)
   * @see DictionaryInterface::copyAll()
   */
  public function copyAll($data, $flags = 0)
  {
    return $this->_copyAll($data, $flags);
  }

  /**
   * (non-PHPdoc)
   * @see DictionaryInterface::del()
   */
  public function del($name)
  {
    return $this->_del($name);
  }

  /**
   * (non-PHPdoc)
   * @see DictionaryInterface::get()
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
   * @see DictionaryInterface::getAll()
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
   * @see DictionaryInterface::getNames()
   */
  public function getNames()
  {
    $names = array_keys($this->data);
    return $names;
  }

  /**
   * (non-PHPdoc)
   * @see DictionaryInterface::has()
   */
  public function has($name, $flags = 0)
  {
    return $this->_has($name);
  }

  /**
   * Create Dictionary from $data
   *
   * If $data is already a Dictionary, it is returned unchanged.
   *
   * @param mixed $data
   * @return Dictionary
   */
  public static function parseFrom($data)
  {
    if (!($data instanceof DictionaryInterface)) {
      $data = new Dictionary($data);
    }

    return $data;
  }

  /**
   * (non-PHPdoc)
   * @see DictionaryInterface::reset()
   */
  public function reset($data = null)
  {
    $this->data = array();
    return $this->copyAll($data);
  }

  /**
   * (non-PHPdoc)
   * @see DictionaryInterface::set()
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
   * @return Dictionary
   */
  public function setData(&$data)
  {
    $this->data = & $data;
    return $this;
  }

  /**
   * (non-PHPdoc)
   * @see DictionaryInterface::copyAll()
   */
  protected function _copyAll($data, $flags = 0)
  {
    if (empty($data))
      return;

    if ($data instanceof DictionaryInterface) {
      $data = $data->getAll();
    }

    foreach ($data as $name => $value) {
      if ((Dictionary::FILTER_NULL & $flags) == $flags && is_null($value))
        continue;

      if ((Dictionary::FILTER_EMPTY & $flags) == $flags && empty($value))
        continue;

      $this->set($name, $value);
    }

    return $this;
  }

  /**
   * (non-PHPdoc)
   * @see DictionaryInterface::get()
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
   * @see DictionaryInterface::get()
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
   * @see DictionaryInterface::has()
   */
  protected function _has($name, $flags = 0)
  {
    if (!isset($this->data[$name])) {
      return false;
    }

    if ((Dictionary::FILTER_NULL & $flags) == $flags && is_null($this->data[$name])) {
      return false;
    }

    if ((Dictionary::FILTER_EMPTY & $flags) == $flags && empty($this->data[$name])) {
      return false;
    }

    return true;
  }

  /**
   * (non-PHPdoc)
   * @see DictionaryInterface::set()
   */
  protected function _set($name, $value)
  {
    $this->data[$name] = $value;
    return $this;
  }

  /**
   * (non-PHPdoc)
   * @see JsonSerializable::jsonSerialize()
   */
  public function jsonSerialize()
  {
    return $this->data;
  }

}
