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
 *
 * @author Rodrigo Rutkoski Rodrigues, <rutkoski@gmail.com>
 * @package Simplify_Kernel
 */
abstract class Simplify_Renderable extends Simplify_Dictionary implements Simplify_RenderableInterface
{

  /**
   * @var string
   */
  protected $template;

  /**
   * @var mixed
   */
  protected $layout = false;

  /**
   * @var IView
   */
  protected $view;

  /**
   *
   * @param string $type
   * @return View
   */
  public function getView($type = null)
  {
    if (empty($this->view)) {
      if (is_null($type)) {
        switch (true) {
          case s::request()->json() :
            $type = Simplify_View::JSON;
            break;

          case s::request()->xml() :
            $type = Simplify_View::PHP;
            break;
        }
      }

      $this->view = Simplify_View::factory($type, $this);
    }

    return $this->view;
  }

  /**
   * (non-PHPdoc)
   * @see simplify/kernel/IRenderable#getLayout()
   */
  public function getLayout()
  {
    $filename = empty($this->layout) ? $this->getLayoutFilename() : $this->layout;

    if ((is_null($this->layout) && s::request()->ajax()) || $this->layout === false) {
      return false;
    }

    elseif (sy_path_is_absolute($filename)) {
      $layout = $filename;
    }

    else {
      $path = (array) $this->getLayoutsPath();

      while (count($path)) {
        $layout = array_shift($path) . '/' . $filename . '_layout.php';
        if (file_exists($layout)) break;
      }
    }

    if (! file_exists($layout)) {
      throw new Exception("Layout file not found: <b>{$layout}</b>");
    }

    return $layout;
  }

  public function getLayoutFilename()
  {
    return $this->layout;
  }

  /**
   *
   * @return array|string possible paths to templates, in order of preference
   */
  public function getLayoutsPath()
  {
    return s::config()->get('templates_dir') . '/layouts';
  }

  /**
   * (non-PHPdoc)
   * @see simplify/kernel/IRenderable#getTemplate()
   */
  public function getTemplate()
  {
    $filename = empty($this->template) ? $this->getTemplateFilename() : $this->template;

    if ($this->template === false) {
      return false;
    }

    elseif (empty($filename)) {
      throw new Exception('Template file not set');
    }

    elseif (sy_path_is_absolute($filename)) {
      $template = $filename;
    }

    else {
      $path = (array) $this->getTemplatesPath();

      while (count($path)) {
        $template = array_shift($path) . '/' . $filename . '.php';
        if (file_exists($template)) break;
      }
    }

    if (! file_exists($template)) {
      throw new Exception("Template file not found: <b>{$template}</b>");
    }

    return $template;
  }

  public function getTemplateFilename()
  {
    return $this->template;
  }

  public function getTemplatesPath()
  {
    return s::config()->get('templates_dir');
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
      $output = $this->getView()->render();
    }
    catch (Exception $e) {
      sy_exception_handler($e);

      $output = '';
    }

    return $output;
  }

}
