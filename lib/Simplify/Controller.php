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
 * Basic controller
 *
 */
abstract class Simplify_Controller extends Simplify_Renderable
{

  /**
   * Default controller action name
   * 
   * @var string
   */
  const ACTION_DEFAULT = 'index';

  /**
   * Action name
   * 
   * @var string
   */
  protected $name;

  /**
   * Current action
   * 
   * @var string
   */
  protected $action;

  /**
   * Controller path
   * 
   * @var string
   */
  protected $path;

  /**
   * Controller module
   * 
   * @var string
   */
  protected $module;

  /**
   * Current layout
   * 
   * @var string
   */
  protected $layout = 'default';

  /**
   * Constructor
   * 
   * @return void
   */
  public function __construct()
  {
    $this->initialize();
  }

  /**
   * Initialize callback runs once
   * 
   * @return void
   */
  protected function initialize()
  {
  }

  /**
   * This callback runs once after every action
   *
   * @param mixed $output the action output
   * @return mixed
   */
  protected function afterAction($output)
  {
  }

  /**
   * This callback runs once before every action
   *
   * @return mixed
   */
  protected function beforeAction()
  {
  }

  /**
   * Default action
   *
   * @return mixed
   */
  protected function indexAction()
  {
  }

  /**
   * Order the named params in the route to match the action params
   * 
   * @param string $action the action
   * @param array $params action params
   * @throws BadMethodCallException if any action parameter is missing 
   * @return array action parameters in the right order 
   */
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
      }
      elseif ($parameter->isDefaultValueAvailable()) {
        $_params[$name] = $parameter->getDefaultValue();
      }
      else {
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

  /**
   * Call an action in the controller and return its result
   * 
   * @param string $action the action
   * @param array $params action parameters
   * @throws RouterException if the action is not found
   * @return mixed
   */
  public function callAction($action, $params = null)
  {
    $func = Inflector::variablize($action . 'Action');
    
    if (!method_exists($this, $func)) {
      throw new Simplify_RouterException('Action not found');
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

  /**
   * Forward the request to another route
   * 
   * @param string $uri the route
   * @return mixed
   */
  public function forward($uri)
  {
    return s::app()->forward($uri);
  }

  /**
   * Get the module name
   * 
   * @return string
   */
  public function getModule()
  {
    return $this->module;
  }

  /**
   * Get the action name
   * 
   * @return string
   */
  public function getAction()
  {
    return $this->action;
  }

  /**
   * Get the controller name
   * 
   * @return string
   */
  public function getName()
  {
    if (empty($this->name)) {
      $this->name = strtolower(substr(get_class($this), 0, strrpos(get_class($this), 'Controller')));
    }
    
    return $this->name;
  }

  /**
   * Get the controller path
   * 
   * @return string
   */
  public function getPath()
  {
    return $this->path;
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Renderable::getLayoutsPath()
   */
  public function getLayoutsPath()
  {
    return array(s::config()->get('modules_dir') . '/' . $this->module . '/templates/layouts', s::config()->get('templates_dir') . '/layouts/' . $this->module, s::config()->get('templates_dir') . '/layouts');
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Renderable::getTemplateFilename()
   */
  public function getTemplateFilename()
  {
    return $this->getName() . '_' . $this->getAction();
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Renderable::getTemplatesPath()
   */
  public function getTemplatesPath()
  {
    return array(s::config()->get('templates_dir') . '/' . $this->module, s::config()->get('modules_dir') . '/' . $this->module . '/templates', s::config()->get('templates_dir'));
  }

}
