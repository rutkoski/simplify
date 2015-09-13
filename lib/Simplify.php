<?php

class Simplify
{

  protected static $app;

  protected static $config;

  protected static $response;

  protected static $router;

  protected static $l10n;

  /**
   * 
   * @param Simplify\Application $app
   * @return \Simplify\Application
   */
  public static function app(Simplify\Application $app = null)
  {
    if (!empty($app)) {
      self::$app = $app;
    }
    elseif (!self::$app) {
      self::$app = new Simplify\Application();
    }
    return self::$app;
  }

  /**
   * 
   * @return \Simplify\Config
   */
  public static function config()
  {
    if (!self::$config) {
      self::$config = new Simplify\Config();
    }
    return self::$config;
  }

  /**
   * Get the dbal object
   *
   * @return Simplify\Db\Database
   */
  public static function db($id = 'default', $engine = null, $params = null)
  {
    return Simplify\Db\Database::getInstance($id, $engine, $params);
  }

  /**
   * 
   * @param \Simplify\Localization $l10n
   * @return Simplify\Localization
   */
  public static function l10n(\Simplify\Localization $l10n = null)
  {
    if (empty(self::$l10n)) {
      if (!empty($l10n)) {
        self::$l10n = $l10n;
      }
      else {
        self::$l10n = new \Simplify\Localization\ArrayLocalization();
      }
    }
  
    return self::$l10n;
  }

  /**
   * 
   * @return \Simplify\Request
   */
  public static function request()
  {
    return Simplify\Request::getInstance();
  }

  /**
   * 
   * @return \Simplify\Response
   */
  public static function response()
  {
    if (!self::$response) {
      self::$response = new Simplify\Response();
    }
    return self::$response;
  }

  /**
   * 
   * @return \Simplify\Router
   */
  public static function router()
  {
    if (!self::$router) {
      self::$router = new Simplify\Router();
    }
    return self::$router;
  }

  /**
   * 
   * @return \Simplify\Session
   */
  public static function session()
  {
    return Simplify\Session::getInstance();
  }

}
