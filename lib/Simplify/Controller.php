<?php

abstract class Simplify_Controller extends Simplify_Renderable
{

  const ACTION_DEFAULT = 'index';

  protected $name;

  protected $action;

  protected $path;

  protected $module;

  protected $layout = 'default';

  public function __construct()
  {
    $this->initialize();
  }

  protected function initialize()
  {
    //
  }

  protected function afterAction($output)
  {
    //
  }

  protected function beforeAction()
  {
    //
  }

  protected function orderParams($action, $params)
  {
    $controller = $this->getName();

    $func = Inflector::variablize($action . 'Action');

    $method = new ReflectionMethod($this, $func);
    $parameters = $method->getParameters();

    $_params = array();

    foreach ($parameters as $parameter) {
      $name = $parameter->name;

      if (isset($params[$name])) {
        $_params[$name] = $params[$name];

        unset($params[$name]);
      } elseif ($parameter->isDefaultValueAvailable()) {
        $_params[$name] = $parameter->getDefaultValue();
      } else {
        throw new BadMethodCallException("Missing parameter <b>{$name}</b> on action <b>{$action}</b> of controller <b>{$controller}</b>");
      }
    }

    unset($parameters, $method);

    foreach ($params as $name => $param) {
      if (is_numeric($name)) {
        $_params[$name] = $param;
      }
    }

    return $_params;
  }

  public function callAction($action, $params = null)
  {
    $func = Inflector::variablize($action . 'Action');

    if (! method_exists($this, $func)) {
      throw new RouterException('Action not found');
    }

    $this->action = $action;

    $params = $this->orderParams($action, (array) $params);

    $this->beforeAction();

    $output = call_user_func_array(array($this, $func), (array) $params);

    $this->afterAction($output);

    if ($output === Simplify_Response::AUTO) {
      $output = $this->getView();
    }

    return $output;
  }

  public function forward($uri)
  {
    return s::app()->forward($uri);
  }

  public function getModule()
  {
    return $this->module;
  }

  public function getAction()
  {
    return $this->action;
  }

  public function getName()
  {
    if (empty($this->name)) {
      $this->name = strtolower(substr(get_class($this), 0, strrpos(get_class($this), 'Controller')));
    }

    return $this->name;
  }

  public function getPath()
  {
    return $this->path;
  }

  public function getLayoutsPath()
  {
    return array(
      s::config()->get('modules_dir') . '/' . $this->module . '/templates/layouts',
      s::config()->get('templates_dir') . '/layouts/' . $this->module,
      s::config()->get('templates_dir') . '/layouts',
    );
  }

  public function getTemplateFilename()
  {
    return $this->getName() . '_' . $this->getAction();
  }

  public function getTemplatesPath()
  {
    return array(
      s::config()->get('templates_dir'). '/' . $this->module,
      s::config()->get('modules_dir') . '/' . $this->module . '/templates',
      s::config()->get('templates_dir'),
    );
  }

  protected function indexAction()
  {
    //
  }

}
