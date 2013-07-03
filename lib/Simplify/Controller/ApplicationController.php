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
 * Default Application Controller
 *
 */
class Simplify_Controller_ApplicationController
{

  /**
   * Execute current request
   *
   * @return controller/action result
   */
  public function dispatch()
  {
    try {
      $output = $this->forward(s::request()->route());

      s::response()->output($output);

      return $output;
    }
    catch (Simplify_RouterException $e) {
      if (! sy_debug_level()) {
        s::response()->redirect404();
      }

      throw $e;
    }
  }

  /**
   * Call the request specified by the route uri
   *
   * @param string $uri the route uri
   * @return mixed action result
   */
  public function forward($uri)
  {
    $route = s::router()->parse($uri);

    $controller = $this->factory($route);

    $action = sy_get_param($route, Simplify_Router::ACTION, Simplify_Controller::ACTION_DEFAULT);
    $params = sy_get_param($route, Simplify_Router::PARAMS, array());

    return $controller->callAction($action, $params);
  }

  /**
   * Factory a controller from route parameters
   *
   * @return Controller
   */
  public function factory($params)
  {
    $module = sy_get_param($params, Simplify_Router::MODULE);
    $controller = Simplify_Inflector::camelize(sy_get_param($params, Simplify_Router::CONTROLLER));

    $path = isset($params['path']) ? DIRECTORY_SEPARATOR . $params['path'] : '';
    $class = $controller . 'Controller';
    $filename = $class . '.php';

    if ($module) {
      $base = s::config()->get('modules:' . $module . ':dir', s::config()->get('modules:dir') . '/' . $module, Simplify_Dictionary::FILTER_NULL);

      $filename = $base . DIRECTORY_SEPARATOR . 'controller' . $path . DIRECTORY_SEPARATOR . $filename;
    }

    else {
      $modules = s::config()->get('modules');

      reset($modules);
      do {
        $module = key($modules);
        $base = s::config()->get('modules:' . $module . ':dir');
        $filename = $base . DIRECTORY_SEPARATOR . 'controller' . $path . DIRECTORY_SEPARATOR . $filename;
        $valid = next($module) !== false;
      }
      while (! file_exists($filename) && $valid);
    }

    if (! file_exists($filename)) {
     throw new Exception("Could not factory controller: file not found: <b>{$filename}</b>");
    }

    require_once($filename);

    if (! class_exists($class)) {
      throw new Exception("Could not factory controller: class not found: <b>{$class}</b>");
    }

    $Controller = new $class();
    $Controller->setBase($base);
    $Controller->setPath($path);
    $Controller->setModule($module);

    return $Controller;
  }

}
