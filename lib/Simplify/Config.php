<?php

class Simplify_Config implements Simplify_DictionaryInterface, ArrayAccess
{

  protected $data = array();

  /**
   *
   * ArrayAccess implementation
   *
   */

  public function offsetExists($offset)
  {
    return $this->has($offset);
  }

  public function & offsetGet($offset)
  {
    return $this->get($offset);
  }

  public function offsetSet($offset, $value)
  {
    $this->set($offset, $value);
  }

  public function offsetUnset($offset)
  {
    $this->del($offset);
  }

  /**
   *
   * Simplify_DictionaryInterface implementation
   *
   */

  public function copyAll($data, $flags = 0)
  {
    foreach ($data as $name => &$value) {
      $this->set($name, $value);
    }
  }

  public function del($name)
  {
    $ref =& $this->data;

    $sub = $name;
    while (($i = strpos($sub, ':')) !== false) {
      $name = substr($sub, 0, $i);
      $sub = substr($sub, $i + 1);
      $ref =& $ref[$name];
    }

    unset($ref[$sub]);
  }

  public function & get($name, $default = null, $flags = 0)
  {
    $ref =& $this->data;

    $sub = $name;
    while (($i = strpos($sub, ':')) !== false) {
      $name = substr($sub, 0, $i);
      $sub = substr($sub, $i + 1);
      $ref =& $ref[$name];
    }

    $value =& $ref[$sub];

    if (is_string($value)) {
      $value = $this->resolveReferences($value);
    }

    return $value;
  }

  public function getAll($flags = 0)
  {
    return $this->data;
  }

  public function & getData()
  {
    return $this->data;
  }

  public function getNames()
  {
    return array_keys($this->data);
  }

  public function has($name, $flags = 0)
  {
    return isset($this->data[$name]);
  }

  public function reset($data = null)
  {
    $this->data = array();
    return $this->copyAll($data);
  }

  public function set($name, $value = null)
  {
    $ref =& $this->data;

    $sub = $name;
    while (($i = strpos($sub, ':')) !== false) {
      $name = substr($sub, 0, $i);
      $sub = substr($sub, $i + 1);
      $ref =& $ref[$name];
    }

    if (! is_numeric($sub) && empty($sub)) {
      $ref[] = & $value;
    } else {
      $ref[$sub] = & $value;
    }
  }

  public function setData(&$data)
  {
    $this->data =& $data;
  }

  /**
   *
   * protected
   *
   */

  protected function resolveReferences($value)
  {
    while (($s = strpos($value, '{')) !== false) {
      if (($f = strpos($value, '}', $s)) !== false) {
        $name = substr($value, $s + 1, $f - $s - 1);
        $value = str_replace('{' . $name . '}', $this->get($name), $value);
      }
    }

    return $value;
  }

}
