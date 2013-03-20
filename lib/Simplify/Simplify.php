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
 * The Simplify facade class provides static access to the main components
 *
 */
class s
{

  /**
   * @var Simplify_Config
   */
  protected static $config;

  /**
   * @var Simplify_Controller_ApplicationController
   */
  protected static $application;

  /**
   * @var Simplify_Response
   */
  protected static $response;

  /**
   * @var Simplify_Router
   */
  protected static $router;

  /**
   * @var array
   */
  protected static $handlers;

  /**
   * @var array
   */
  protected static $actions;

  /**
   *
   * @return Simplify_Controller_ApplicationController
   */
  public static function app(Simplify_Controller_ApplicationController $app = null)
  {
    if (empty(self::$application)) {
      if (! empty($app)) {
        self::$application = $app;
      }
      else {
        self::$application = new Simplify_Controller_ApplicationController();
      }
    }

    return self::$application;
  }

  /**
   *
   * @return Simplify_Db_Database
   */
  public static function db($id = 'default', $engine = null, $params = null)
  {
    return Simplify_Db_Database::getInstance($id, $engine, $params);
  }

  /**
   *
   * @return Simplify_Config
   */
  public static function config()
  {
    if (empty(self::$config)) {
      self::$config = new Simplify_Config();
    }

    return self::$config;
  }

  /**
   *
   * @return Simplify_Request
   */
  public static function request()
  {
    return Simplify_Request::getInstance();
  }

  /**
   *
   * @return Simplify_Session
   */
  public static function session()
  {
    return Simplify_Session::getInstance();
  }

  /**
   *
   * @return Simplify_Router
   */
  public static function router()
  {
    if (empty(self::$router)) {
      $router = new Simplify_Router();

      if (s::config()->has('routes')) {
        $routes = s::config()->get('routes');

        foreach ($routes as $id => $route) {
          $router->connect(sy_get_param($route, 0), sy_get_param($route, 1), sy_get_param($route, 2));
        }
      }

      self::$router = $router;
    }

    return self::$router;
  }

  /**
   *
   * @return Simplify_Response
   */
  public static function response()
  {
    if (empty(self::$response)) {
      self::$response = new Simplify_Response();
    }

    return self::$response;
  }

  /**
   *
   * @param string $hook hook name
   * @param function $callback valid php callback
   * @param ... extra parameters
   */
  public static function addAction($hook, $callback)
  {
    $args = array_slice(func_get_args(), 2);
    self::$actions[$hook][] = array($callback, $args);
  }

  /**
   *
   * @param string $hook hook name
   */
  public static function callAction($hook)
  {
    if (isset(self::$actions[$hook])) {
      $_args = array_slice(func_get_args(), 1);

      foreach (self::$actions[$hook] as $action) {
        $args = array_merge($action[1], $_args);
        call_user_func_array($action[0], $args);
      }
    }
  }

}
