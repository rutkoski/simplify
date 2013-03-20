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
 *
 * @author Rodrigo Rutkoski Rodrigues <rutkoski@gmail.com>
 */
class Simplify_Request
{

  const GET = 'GET';

  const POST = 'POST';

  /**
   *
   * @var string
   */
  protected $route;

  /**
   *
   * @var string
   */
  protected $extension;

  /**
   *
   * @var boolean
   */
  protected $pretty;

  /**
   *
   * @var DataView
   */
  protected $post;

  /**
   *
   * @var DataView
   */
  protected $get;

  /**
   *
   * @var Simplify_Request
   */
  protected static $instance;

  /**
   *
   * @return Simplify_Request
   */
  public static function getInstance()
  {
    if (empty(self::$instance)) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  private function __construct()
  {
    $this->parse();
  }

  /**
   *
   * @return Simplify_Request
   */
  protected function parse()
  {
    $dirname = dirname($_SERVER['SCRIPT_NAME']);

    $regex = '#' . $dirname . '(?:(' . quotemeta($this->self()) . '))?(.*?)(?:\.([^\?]+))?(?:\?.*)?/*$#';

    if (! preg_match($regex, $this->uri(), $o)) {
      throw new Exception('Could not parse url');
    }

    $this->pretty = empty($o[1]);
    $this->route = empty($o[2]) ? '/' : $o[2];
    $this->extension = sy_get_param($o, 3);
  }

  /**
   *
   * @return string
   */
  public function pretty()
  {
    return $this->pretty;
  }

  /**
   *
   * @return string
   */
  public function method($method = null)
  {
    if (! is_null($method)) {
      return $_SERVER['REQUEST_METHOD'] == strtoupper($method);
    }

    return $_SERVER['REQUEST_METHOD'];
  }

  /**
   *
   * @return boolean
   */
  public function ajax()
  {
    return getenv('HTTP_X_REQUESTED_WITH') === "XMLHttpRequest";
  }

  /**
   *
   * @return boolean
   */
  public function json()
  {
    return $this->extension == 'json';
  }

  /**
   *
   * @return boolean
   */
  public function xml()
  {
    return $this->extension == 'xml';
  }

  /**
   *
   */
  public function post($name = null, $default = null, $flags = 0)
  {
    if (! $this->post) {
      $this->post = new Simplify_Data_View(sy_strip_slashes_deep($_POST));
    }

    if (! is_null($name)) {
      return $this->post->get($name, $default, $flags);
    }

    return $this->post;
  }

  /**
   *
   */
  public function get($name = null, $default = null, $flags = 0)
  {
    if (! $this->get) {
      $this->get = new Simplify_Data_View(sy_strip_slashes_deep($_GET));
    }

    if (! is_null($name)) {
      return $this->get->get($name, $default, $flags);
    }

    return $this->get;
  }

  /**
   *
   * @return string
   */
  public function route()
  {
    return $this->route;
  }

  /**
   *
   * @return string
   */
  public function extension()
  {
    return $this->extension;
  }

  /**
   *
   * @return string
   */
  public function uri()
  {
    if (! isset($_SERVER['REQUEST_URI'])) {
      if (isset($_SERVER['REQUEST_URI'])) {
        $_SERVER['REQUEST_URI'] = $_SERVER['REQUEST_URI'];
      }
      elseif (isset($_SERVER['QUERY_STRING'])) {
        $_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['QUERY_STRING'];
      }
      else {
        $_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
      }
    }

    if (isset($_SERVER['REQUEST_URI'])) {
      $uri = $_SERVER['REQUEST_URI'];
    }
    else {
      $uri = $_SERVER['ORIG_PATH_INFO'];
    }

    return $uri;
  }

  /**
   *
   * @return string
   */
  public function base()
  {
    return ($this->secure() ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
  }

  /**
   *
   * @return string
   */
  public function url()
  {
    $dir = dirname($_SERVER['SCRIPT_NAME']);
    if ($dir == '/') $dir = '';
    return $this->base() . $dir;
  }

  /**
   *
   * @return boolean
   */
  public function secure()
  {
    return empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
  }

  /**
   *
   * @return string
   */
  public function self()
  {
    return basename($_SERVER['SCRIPT_NAME']);
  }

  /**
   *
   * @return string
   */
  public function ip()
  {
    if (! empty($_SERVER['HTTP_CLIENT_IP'])) {
      $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (! empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else {
      $ip = $_SERVER['REMOTE_ADDR'];
    }

    return $ip;
  }

}
