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
 * Simplify config
 *
 */
class Simplify_Config implements Simplify_DictionaryInterface, ArrayAccess
{

  protected $data = array();

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
   * @see ArrayAccess::offsetGet()
   */
  public function &offsetGet($offset)
  {
    return $this->get($offset);
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
   * @see ArrayAccess::offsetUnset()
   */
  public function offsetUnset($offset)
  {
    $this->del($offset);
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_DictionaryInterface::copyAll()
   */
  public function copyAll($data, $flags = 0)
  {
    foreach ($data as $name => &$value) {
      $this->set($name, $value);
    }
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_DictionaryInterface::del()
   */
  public function del($name)
  {
    $ref = & $this->data;

    $sub = $name;
    while (($i = strpos($sub, ':')) !== false) {
      $name = substr($sub, 0, $i);
      $sub = substr($sub, $i + 1);
      $ref = & $ref[$name];
    }

    unset($ref[$sub]);
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_DictionaryInterface::get()
   */
  public function get($name, $default = null, $flags = 0)
  {
    $ref = & $this->data;

    $sub = $name;
    while (($i = strpos($sub, ':')) !== false) {
      $name = substr($sub, 0, $i);
      $sub = substr($sub, $i + 1);
      $ref = & $ref[$name];
    }

    if ($flags == Simplify_Dictionary::FILTER_NULL && is_null($ref[$sub])) {
      $value = $default;
    } elseif ($flags == Simplify_Dictionary::FILTER_EMPTY && empty($ref[$sub])) {
      $value = $default;
    } else {
      $value = & $ref[$sub];
    }

    if (is_string($value)) {
      $value = $this->resolveReferences($value);
    }

    return $value;
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_DictionaryInterface::getAll()
   */
  public function getAll($flags = 0)
  {
    return $this->data;
  }

  /**
   *
   * @return array
   */
  public function &getData()
  {
    return $this->data;
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_DictionaryInterface::getNames()
   */
  public function getNames()
  {
    return array_keys($this->data);
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_DictionaryInterface::has()
   */
  public function has($name, $flags = 0)
  {
    return isset($this->data[$name]);
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
  public function set($name, $value = null)
  {
    $ref = & $this->data;

    $sub = $name;
    while (($i = strpos($sub, ':')) !== false) {
      $name = substr($sub, 0, $i);
      $sub = substr($sub, $i + 1);
      $ref = & $ref[$name];
    }

    if (!is_numeric($sub) && empty($sub)) {
      $ref[] = & $value;
    }
    else {
      $ref[$sub] = & $value;
    }
  }

  /**
   *
   * @param array $data
   */
  public function setData(&$data)
  {
    $this->data = & $data;
  }

  /**
   * Resolve internal references on config value
   *
   * @param string $value
   * @return mixed
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
