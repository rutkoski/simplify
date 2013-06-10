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
 * URL builder
 *
 */
class Simplify_URL
{

  const JSON = 'json';

  /**
   *
   * @var Simplify_URL
   */
  protected $extend;

  /**
   * Add $_GET parameters to the new url
   *
   * @var boolean
   */
  protected $keepOriginal;

  /**
   *
   * @var string
   */
  protected $route;

  /**
   *
   * @var string
   */
  protected $format;

  /**
   *
   * @var array
   */
  protected $params;

  /**
   *
   * @var array
   */
  protected $remove;

  /**
   *
   * @return void
   */
  public function __construct($route = null, array $params = null, $keepOriginal = null, array $remove = null, $format = null)
  {
    $this->route = $route;
    $this->params = (array) $params;
    $this->keepOriginal = $keepOriginal;
    $this->remove = (array) $remove;
    $this->format = $format;
  }

  /**
   *
   * @return Simplify_URL
   */
  public function format($format)
  {
    $this->format = $format;
    return $this;
  }

  /**
   *
   * @return Simplify_URL
   */
  public static function make($route = null, array $params = null, $keepOriginal = null, array $remove = null, $format = null)
  {
    $url = new self($route, $params, $keepOriginal, $remove, $format);
    return $url;
  }

  /**
   *
   * @return Simplify_URL
   */
  public static function parse($url)
  {
    if ($url instanceof Simplify_URL) {
      return $url;
    }

    if (is_string($url)) {
      return new self($url);
    }

    if (is_array($url)) {
      return call_user_func_array(array('self', 'make'), $url);
    }

    return new self();
  }

  /**
   *
   * @return Simplify_URL
   */
  public function extend($route = null, array $params = null, $keepOriginal = null, array $remove = null, $format = null)
  {
    $url = new self($route, $params, $keepOriginal, $remove, $format);
    $url->extend = $this;
    return $url;
  }

  /**
   *
   * @return Simplify_URL
   */
  public function copy()
  {
    $url = new self();
    $url->keepOriginal = $this->keepOriginal;
    $url->route = $this->route;
    $url->params = $this->params;
    $url->remove = $this->remove;
    $url->format = $this->format;
    return $url;
  }

  /**
   *
   * @param null|boolean $keep
   * @return Simplify_URL
   */
  public function keepOriginal($keep = true)
  {
    $this->keepOriginal = $keep;
    return $this;
  }

  /**
   *
   * @param mixed $remove
   * @return Simplify_URL
   */
  public function remove($remove = null)
  {
    if ($remove === false) {
      $this->remove = null;
    }
    elseif (is_null($remove)) {
      return $this->remove;
    } else {
      foreach (func_get_args() as $remove) {
        if (is_array($remove)) {
          $this->remove = array_merge($this->remove, $remove);
        } else {
          $this->remove[] = $remove;
        }
      }
    }

    return $this;
  }

  /**
   *
   * @param string $name
   * @return mixed
   */
  public function get($name)
  {
    return $this->params[$name];
  }

  /**
   *
   * @return Simplify_URL
   */
  public function set($name, $value)
  {
    $this->params[$name] = $value;
    return $this;
  }

  /**
   *
   * @return string
   */
  public function build()
  {
    $route = $this->_route();

    $url = $route;

    if (strpos($url, 'javascript:') === 0) {
      return $url;
    }

    if (!sy_url_is_absolute($url)) {
      if (empty($route)) {
        $route = s::request()->route();
      }

      while (strpos($route, '//') === 0) {
        $route = str_replace('//', '/', $route);
      }

      while (strpos($route, '/') === strlen($route) - 1) {
        $route = substr($route, 0, strlen($route) - 2);
      }

      if (strpos($route, '/') !== 0) {
        $route = '/' . $route;
      }

      $url = s::request()->url();

      if (!s::request()->pretty()) {
        $url .= '/' . s::request()->self();
      }

      $url .= $route;

      $ext = $this->_format();
      if (empty($ext) && $ext !== false) {
        $ext = s::request()->extension();
      }

      if (!empty($ext)) {
        $url .= '.' . $ext;
      }
    }

    $params = $this->_params();

    if ($this->_keepOriginal()) {
      $keep = s::request()->get()->getAll();
      $params = array_merge($keep, $params);
    }

    $remove = $this->_remove();
    if (!empty($remove)) {
      $keep = array_diff_key($params, array_flip($remove));
      $params = array_intersect_key($params, $keep);
    }

    if (!empty($params)) {
      $url .= '?' . http_build_query($params, null, '&');
    }

    return $url;
  }

  public function __toString()
  {
    return $this->build();
  }

  protected function _keepOriginal()
  {
    return $this->extend ? $this->extend->_keepOriginal() === true || $this->keepOriginal === true : $this->keepOriginal;
  }

  protected function _route()
  {
    return empty($this->route) && $this->extend ? $this->extend->_route() : $this->route;
  }

  protected function _format()
  {
    return empty($this->format) && $this->extend ? $this->extend->_format() : $this->format;
  }

  protected function _params()
  {
    return $this->extend ? array_merge($this->extend->_params(), $this->params) : $this->params;
  }

  protected function _remove()
  {
    return $this->extend ? array_merge($this->extend->_remove(), $this->remove) : $this->remove;
  }

}
