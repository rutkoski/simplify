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
   * @var Simplify_Localization
   */
  protected static $l10n;

  /**
   * Get the application controller object
   *
   * @return Simplify_Controller_ApplicationController
   */
  public static function app(Simplify_Controller_ApplicationController $app = null)
  {
    if (empty(self::$application)) {
      if (!empty($app)) {
        self::$application = $app;
      }
      else {
        self::$application = new Simplify_Controller_ApplicationController();
      }
    }

    return self::$application;
  }

  /**
   * Get the dbal object
   *
   * @return Simplify_Db_Database
   */
  public static function db($id = 'default', $engine = null, $params = null)
  {
    return Simplify_Db_Database::getInstance($id, $engine, $params);
  }

  /**
   * Get the config object
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
   * Get the request object
   *
   * @return Simplify_Request
   */
  public static function request()
  {
    return Simplify_Request::getInstance();
  }

  /**
   * Get the session object
   *
   * @return Simplify_Session
   */
  public static function session()
  {
    return Simplify_Session::getInstance();
  }

  /**
   * Get the router object
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
   * Get the response
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
   * Get the localization object
   *
   * @return Simplify_Localization
   */
  public static function l10n(Simplify_Localization $l10n = null)
  {
    if (empty(self::$l10n)) {
      if (!empty($l10n)) {
        self::$l10n = $l10n;
      }
      else {
        self::$l10n = new Simplify_Localization_Array();
      }
    }

    return self::$l10n;
  }

}
