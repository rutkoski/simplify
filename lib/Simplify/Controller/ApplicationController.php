<?php

class Simplify_Controller_ApplicationController
{

  /**
   * Execute current request.
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
   * Factory a controller from route parameters.
   *
   * @return Controller
   */
  public function factory($params)
  {
    $module = sy_get_param($params, Simplify_Router::MODULE);//Inflector::camelize(sy_get_param($params, Simplify_Router::MODULE));
    $controller = Inflector::camelize(sy_get_param($params, Simplify_Router::CONTROLLER));

    $path = isset($params['path']) ? DIRECTORY_SEPARATOR . $params['path'] : '';
    $class = $controller . 'Controller';
    $filename = $class . '.php';

    if ($module) {
      $base = s::config()->get('modules:' . $module . ':path') . DIRECTORY_SEPARATOR . 'controller';

      $filename = $base . $path . DIRECTORY_SEPARATOR . $filename;

      if (! file_exists($filename)) {
        throw new Exception("Could not factory controller <b>{$controller}</b>: file not found");
      }

      require_once($filename);
    }

    else {
      $modules = s::config()->get('modules');

      foreach ($modules as $k => $v) {
        $base = s::config()->get('modules:' . $k . ':path') . DIRECTORY_SEPARATOR . 'controller';

        $filename = $base . $path . DIRECTORY_SEPARATOR . $filename;

        if (file_exists($filename)) {
          require_once($filename);

          break;
        }
      }
    }

    if (! class_exists($class)) {
      throw new Exception("Could not factory controller <b>{$controller}</b>: class not found");
    }

    $Controller = new $class();
    $Controller->path = $path;
    $Controller->module = $module;

    return $Controller;
  }

  /**
   * Factory a controller from route parameters.
   *
   * @return Controller
   */
  public function factory2($params)
  {
    $module = sy_get_param($params, Simplify_Router::MODULE);//Inflector::camelize(sy_get_param($params, Simplify_Router::MODULE));
    $controller = Inflector::camelize(sy_get_param($params, Simplify_Router::CONTROLLER));

    $class = $controller . 'Controller';

    if ($module) {
      $base = s::config()->get('modules_dir') . DIRECTORY_SEPARATOR . $module;

      if (file_exists($base . DIRECTORY_SEPARATOR . 'module.php')) {
        require_once($base . DIRECTORY_SEPARATOR . 'module.php');
      }
    } else {
      $base = APP_DIR;
    }

    $base = $base . DIRECTORY_SEPARATOR . 'controller';

    $path = (isset($params['path']) ? DIRECTORY_SEPARATOR . $params['path'] : '');

    $filename = $base . $path . DIRECTORY_SEPARATOR . $class . '.php';

    if (! file_exists($filename)) {
      throw new Exception("Could not factory controller <b>{$controller}</b>: file not found");
    }

    require_once($filename);

    if (! class_exists($class)) {
      throw new Exception("Could not factory controller <b>{$controller}</b>: class not found");
    }

    $Controller = new $class();
    $Controller->path = $path;
    $Controller->module = $module;

    return $Controller;
  }

}
