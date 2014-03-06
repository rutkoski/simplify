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
 * Handles request information and data
 *
 */
class Simplify_Request
{

  /**
   * Constant value for get request method
   *
   * @var string
   */
  const GET = 'GET';

  /**
   * Constant value for post request method
   *
   * @var string
   */
  const POST = 'POST';

  /**
   * Constant value for put request method
   *
   * @var string
   */
  const PUT = 'PUT';

  /**
   * Constant value for delete request method
   *
   * @var string
   */
  const DELETE = 'DELETE';

  /**
   * Request route
   *
   * @var string
   */
  protected $route;

  /**
   * Request extension
   *
   * @var string
   */
  protected $extension;

  /**
   * The request url is a pretty url (no *.php)
   *
   * @var boolean
   */
  protected $pretty;

  /**
   * Dictionary representation of the post data
   *
   * @var Simplify_Data_View
   */
  protected $post;

  /**
   * Dictionary representation of the get data
   *
   * @var Simplify_Data_View
   */
  protected $get;

  /**
   * Singleton instance of Simplify_Request
   *
   * @var Simplify_Request
   */
  protected static $instance;

  /**
   * Get the singleton instance of Simplify_Request
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

  /**
   * Private constructor.
   *
   * @return void
   */
  private function __construct()
  {
    $this->parse();
  }

  /**
   * Parse and obtain request information
   *
   * @return void
   */
  protected function parse()
  {
    $dirname = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));

    $regex = '#' . $dirname . '(?:(' . quotemeta($this->self()) . '))?(.*?)(?:\.([^\?]+))?(?:\?.*)?/*$#';

    if (!preg_match($regex, $this->uri(), $o)) {
      throw new Exception('Could not parse url');
    }

    $this->pretty = empty($o[1]);
    $this->route = empty($o[2]) ? '/' : $o[2];
    $this->extension = sy_get_param($o, 3);
  }

  /**
   *
   * @return boolean
   */
  public function pretty()
  {
    return $this->pretty;
  }

  /**
   * Get the request method (get, post, put, delete...) or test if it is equal to $method
   *
   * @param string $method the request method to test for
   * @return boolean|string
   */
  public function method($method = null)
  {
    if (!is_null($method)) {
      return $_SERVER['REQUEST_METHOD'] == strtoupper($method);
    }

    return $_SERVER['REQUEST_METHOD'];
  }

  /**
   * Test if it is an ajax request
   *
   * @return boolean
   */
  public function ajax()
  {
    return getenv('HTTP_X_REQUESTED_WITH') === "XMLHttpRequest";
  }

  /**
   * Test if it is a json request
   *
   * @return boolean
   */
  public function json()
  {
    return $this->extension == 'json';
  }

  /**
   * Test if it is an xml request
   *
   * @return boolean
   */
  public function xml()
  {
    return $this->extension == 'xml';
  }

  /**
   * Get the post data or a post var
   *
   * @param string $name
   * @param mixed $default
   * @param int $flags
   * @return mixed|Simplify_Data_View
   */
  public function post($name = null, $default = null, $flags = 0)
  {
    if (!$this->post) {
      $this->post = new Simplify_Data_View(sy_strip_slashes_deep($_POST));
    }

    if (!is_null($name)) {
      return $this->post->get($name, $default, $flags);
    }

    return $this->post;
  }

  /**
   * Get the get data or a get var
   *
   * @param string $name
   * @param mixed $default
   * @param int $flags
   * @return mixed|Simplify_Data_View
   */
  public function get($name = null, $default = null, $flags = 0)
  {
    if (!$this->get) {
      $this->get = new Simplify_Data_View(sy_strip_slashes_deep($_GET));
    }

    if (!is_null($name)) {
      return $this->get->get($name, $default, $flags);
    }

    return $this->get;
  }

  /**
   * Get data from $_FILES
   *
   * @param string $name form field name
   * @return array
   */
  public function files($name = null)
  {
    if (! empty($name)) {
      return $_FILES[$name];
    }
    return $_FILES;
  }

  /**
   * Get the request route
   *
   * @return string
   */
  public function route()
  {
    return $this->route;
  }

  /**
   * Get the request extension
   *
   * @return string
   */
  public function extension()
  {
    return $this->extension;
  }

  /**
   * Get the request uri
   *
   * @return string
   */
  public function uri()
  {
    if (!isset($_SERVER['REQUEST_URI'])) {
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
   * Get the base url
   *
   * @return string
   */
  public function base()
  {
    return ($this->secure() ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
  }

  /**
   * Get the full url
   *
   * @return string
   */
  public function url()
  {
    $dir = dirname($_SERVER['SCRIPT_NAME']);
    if ($dir == '/')
      $dir = '';
    return $this->base() . $dir;
  }

  /**
   * Test if it is an https request
   *
   * @return boolean
   */
  public function secure()
  {
    return empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
  }

  /**
   * Get the request script name
   *
   * @return string
   */
  public function self()
  {
    return basename($_SERVER['SCRIPT_NAME']);
  }

  /**
   * Get the ip address of the request client
   *
   * @return string
   */
  public function ip()
  {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
      $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else {
      $ip = $_SERVER['REMOTE_ADDR'];
    }

    return $ip;
  }

}
