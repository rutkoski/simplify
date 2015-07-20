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

namespace Simplify;

use Simplify;

/**
 *
 * Basic controller
 *
 */
abstract class Controller extends Renderable
{

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
   * Current layout
   *
   * @var string
   */
  protected $layout = 'layout/default';

  /**
   * Constructor
   *
   * @return void
   */
  public function __construct()
  {
    $this->initialize();
  }

  public function callAction($action, $params)
  {
    $method = strtolower(Simplify::request()->method());

    $Action = Inflector::variablize("{$method}_{$action}_action");

    if (! method_exists($this, $Action)) {
      $Action = Inflector::variablize("{$action}_action");

      if (! method_exists($this, $Action)) {
        throw new \Exception("Action <b>{$action}</b> not found in controller <b>{$this->getPath()}\\{$this->getName()}</b>");
      }
    }

    $this->action = $action;

    $func = array($this, $Action);

    $output = call_user_func_array($func, Dispatcher::sortCallbackParameters($func, $params));

    if ($output === Response::AUTO) {
      $output = $this->getView();
    }

    return $output;
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
   * Default action
   *
   * @return mixed
   */
  protected function indexAction()
  {
  }

  /**
   * Default 404 action
   *
   * @return mixed
   */
  protected function pageNotFoundAction()
  {
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
      $this->name = \Simplify\Inflector::underscore(substr(join('', array_slice(explode('\\', get_class($this)), -1)), 0, -10));
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
    if (empty($this->path)) {
      $this->path = strtolower(join('/', array_slice(explode('\\', get_class($this)), 0, -1)));
    }
    return $this->path;
  }

  /**
   * (non-PHPdoc)
   * @see Renderable::getTemplateFilename()
   */
  public function getTemplateFilename()
  {
    return $this->getPath() . '/' . $this->getName() . '_' . $this->getAction();
  }

}
