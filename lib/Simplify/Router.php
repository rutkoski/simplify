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
 * Router
 *
 */
class Simplify_Router
{

  const MODULE = 'module';

  const CONTROLLER = 'controller';

  const ACTION = 'action';

  const PARAMS = 'params';

  /**
   * @var array
   */
  protected $routes = array();

  /**
   * Build an uri according to named route and parameters
   *
   * @param string $name the route name
   * @param string[] $params route parameters
   * @return string
   */
  public function build($name, $params = null)
  {
    if (! isset($this->routes[$name])) {
      throw new Simplify_RouterException("Could not build route: named route not found: <b>{$name}</b>");
    }

    $this->expand($name);

    $route = $this->routes[$name];

    $params = array_merge($route['defaults'], (array) $params);

    foreach ($route['required'] as $name) {
      if (! isset($params[$name])) {
        throw new Simplify_RouterException("Could not build route: missing required parameter: <b>{$name}</b>");
      }
    }

    foreach ($route['match'] as $name => $regex) {
      if (! preg_match($regex, $params[$name])) {
        throw new Simplify_RouterException("Could not build route: parameter <b>{$name}</b> does not match requirements: <b>{$regex}</b>");
      }
    }

    if (isset($params['extra'])) {
      if (is_array($params['extra'])) {
        $params['extra'] = implode('/', $params['extra']);
      }

      if (strpos($params['extra'], '/') === 0) {
        $params['extra'] = substr($params['extra'], 1);
      }
    }

    $uri = $route['uri'];

    foreach ($route['names'] as $name) {
      $uri = str_replace(':' . $name, $params[$name], $uri);
    }

    return $uri;
  }

  /**
   * Define a route
   *
   * @param string $name route name
   * @param string $uri route uri
   * @param string[] $defaults default values for named parameters
   * @param string[] $match regular expressions for matching parameters
   */
  public function connect($name, $uri, $defaults = array(), $match = array())
  {
    $route = array('uri' => $uri, 'defaults' => $defaults, 'match' => $match);

    if (is_null($name)) {
      $this->routes[] = $route;
    }
    else {
      $this->routes[$name] = $route;
    }
  }

  /**
   * Match an uri against routes and parse its parameters
   *
   * @param string $uri the uri
   * @throws Simplify_RouterException if a route cannot be matched
   * @return array
   */
  public function parse($uri)
  {
    reset($this->routes);

    while (current($this->routes) !== false) {
      $name = key($this->routes);

      $route = $this->match($name, $uri);

      if ($route !== false) {
        break;
      }

      next($this->routes);
    }

    if ($route === false) {
      throw new Simplify_RouterException("Unknown route <b>$route</b>");
    }

    return $this->parseParams($route, $uri);
  }

  /**
   *
   * implementation
   *
   */
  protected function match($name, $uri)
  {
    $this->expand($name);

    return preg_match($this->routes[$name]['regex'], $uri) ? $this->routes[$name] : false;
  }

  protected function expand($name)
  {
    if (!isset($this->routes[$name]['regex'])) {
      $_uri = $this->routes[$name]['uri'];
      $_defaults = $this->routes[$name]['defaults'];
      $_match = $this->routes[$name]['match'];

      $words = array_filter(explode('/', $_uri));

      $_regex = array();
      $_names = array();
      $_required = array();

      $index = 0;

      foreach ($words as &$word) {
        if ($word == '*') {
          $_regex[] = '(?:/(.*))?';
          $_match['extra'] = '#^(.*)?#';
          $_names[$index++] = 'extra';
          $word = ':extra';

          break;
        }
        elseif (strpos($word, ':') === 0) {
          $wildcard = substr($word, 1);

          $_names[$index++] = $wildcard;

          if (isset($_match[$wildcard])) {
            if (isset($_defaults[$wildcard])) {
              $_regex[] = '(?:/(' . $_match[$wildcard] . '))?';
            }
            else {
              $_regex[] = '/(' . $_match[$wildcard] . ')';
              $_required[] = $wildcard;
            }

            $_match[$wildcard] = '#^' . $_match[$wildcard] . '$#';
          }
          elseif (array_key_exists($wildcard, $_defaults)) {
            $_regex[] = '(?:/([^/]+))?';
            $_match[$wildcard] = '#^([^/]+)?$#';
          }
          else {
            $_required[] = $wildcard;
            $_regex[] = '/([^/]+)';
            $_match[$wildcard] = '#^[^/]+$#';
          }
        }
        else {
          $_regex[] = '/' . $word;
        }
      }

      $_uri = '/' . implode('/', $words);

      $_regex = implode('', $_regex);
      $_regex = '#^' . $_regex . '/*$#';

      $this->routes[$name] = array('uri' => $_uri, 'regex' => $_regex, 'names' => $_names, 'defaults' => $_defaults, 'match' => $_match, 'required' => $_required);
    }
  }

  protected function parseParams($route, $uri)
  {
    $params = $route['defaults'];

    $params[Simplify_Router::MODULE] = sy_get_param($route['defaults'], Simplify_Router::MODULE);
    $params[Simplify_Router::CONTROLLER] = sy_get_param($route['defaults'], Simplify_Router::CONTROLLER);
    $params[Simplify_Router::ACTION] = sy_get_param($route['defaults'], Simplify_Router::ACTION);
    $params[Simplify_Router::PARAMS] = sy_get_param($route['defaults'], Simplify_Router::PARAMS, array());

    unset($route['defaults'][Simplify_Router::PARAMS]);

    preg_match($route['regex'], $uri, $match);

    array_shift($match);

    foreach ($route['names'] as $key => &$name) {
      $value = sy_get_param($match, $key, sy_get_param((array) $route['defaults'], $name));

      if ($name == 'extra') {
        $extra = array_filter(explode('/', $value));

        $params[Simplify_Router::PARAMS] += $extra;

        break;
      }
      elseif (in_array($name, array(Simplify_Router::MODULE, Simplify_Router::CONTROLLER, Simplify_Router::ACTION))) {
        $params[$name] = $value;
      }
      else {
        $params[Simplify_Router::PARAMS][$name] = $value;
      }
    }

    foreach ($route['defaults'] as $name => &$value) {
      if (preg_match_all('/^\:([^\/]+)$/', $value, $match)) {
        foreach ($match[1] as &$wildcard) {
          $params[$name] = str_replace(':' . $wildcard, $params[$wildcard], $params[$name]);
        }
      }
    }

    return $params;
  }

}
