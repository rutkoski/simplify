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
 * Abstract view.
 *
 * @author Rodrigo Rutkoski Rodrigues, <rutkoski@gmail.com>
 * @package Simplify_Kernel_View
*/
abstract class Simplify_View extends Simplify_Dictionary implements Simplify_ViewInterface, Simplify_RenderableInterface
{

  const JSON = 'Simplify_View_Json';

  const PHP = 'Simplify_View_Php';

  const XML = 'Simplify_View_Xml';

  /**
   * @var string
   */
  protected $template;

  /**
   * @var mixed
   */
  protected $layout = false;

  /**
   * @var array
   */
  protected $__helpers = array();

  /**
   *
   * @var IApplicationController
  */
  protected $app;

  /**
   *
   * @var Config
   */
  protected $config;

  /**
   *
   * @var ISimplify_Request
   */
  protected $request;

  /**
   *
   * @var IResponse
   */
  protected $response;

  /**
   *
   * @var IRouter
   */
  protected $router;

  /**
   *
   * @var IRenderable
   */
  protected $object;

  /**
   *
   * @return View
   */
  public static function factory($class = null, Simplify_RenderableInterface $object = null)
  {
    if (empty($class)) {
      $class = Simplify_View::PHP;
    }

    return new $class($object);
  }

  public function __construct(Simplify_RenderableInterface $object = null)
  {
    $this->object = $object ? $object : $this;
  }

  /**
   * (non-PHPdoc)
   * @see simplify/kernel/IRenderable#getLayout()
   */
  public function getLayout()
  {
    if ($this->layout === false) {
      return false;
    }

    elseif (empty($this->layout)) {
      $layout = s::config()->get('templates_dir') . '/layouts/layout.php';

      if (! file_exists($layout)) {
        throw new Exception("Default layout file not found: <b>{$layout}</b>");
      }
    }

    elseif (sy_path_is_absolute($this->layout)) {
      $layout = $this->layout;
    }

    else {
      $layout = s::config()->get('templates_dir') . '/layouts/' . $this->layout . '.php';
    }

    if (! file_exists($layout)) {
      throw new Exception("Layout file not found: <b>{$layout}</b>");
    }

    return $layout;
  }

  /**
   * (non-PHPdoc)
   * @see simplify/kernel/IRenderable#getTemplate()
   */
  public function getTemplate()
  {
    global $config;

    if ($this->template === false) {
      return false;
    }

    if (empty($this->template)) {
      throw new Exception('Template file not set');
    }

    elseif (sy_path_is_absolute($this->template)) {
      $template = $this->template;
    }

    else {
      $template = $config['templates_dir'] . '/' . $this->template . '.php';
    }

    if (! file_exists($template)) {
      throw new Exception("Template file not found: <b>{$template}</b>");
    }

    return $template;
  }

  /**
   * (non-PHPdoc)
   * @see simplify/kernel/IRenderable#setTemplate($template)
   */
  public function setTemplate($template)
  {
    $this->template = $template;
  }

  /**
   * (non-PHPdoc)
   * @see simplify/kernel/IRenderable#setLayout($layout)
   */
  public function setLayout($layout)
  {
    $this->layout = $layout;
  }

  public function __toString()
  {
    try {
      $output = $this->render();
    }
    catch (Exception $e) {
      $output = $e->getMessage();
    }

    return $output;
  }

  /**
   * (non-PHPdoc)
   * @see ISimplify_Dictionary::getAll()
   */
  public function getAll($flags = 0)
  {
    return $this->object->data;
  }

  /**
   * (non-PHPdoc)
   * @see ISimplify_Dictionary::get($name)
   */
  protected function _del($name)
  {
    if (isset($this->object->data[$name])) {
      unset($this->object->data[$name]);
    }

    return $this;
  }

  /**
   * (non-PHPdoc)
   * @see ISimplify_Dictionary::get($name)
   */
  protected function _get($name)
  {
    if (isset($this->object->data[$name])) {
      return $this->object->data[$name];
    }
  }

  /**
   * (non-PHPdoc)
   * @see ISimplify_Dictionary::set($name, $value)
   */
  protected function _has($name)
  {
    return isset($this->object->data[$name]);
  }

  /**
   * (non-PHPdoc)
   * @see ISimplify_Dictionary::set($name, $value)
   */
  protected function _set($name, $value)
  {
    $this->object->data[$name] = $value;
    return $this;
  }

}
