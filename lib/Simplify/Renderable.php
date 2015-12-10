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

/**
 *
 * Basic implementation of the renderable interface
 *
 */
abstract class Renderable extends Dictionary implements RenderableInterface
{

  /**
   * @var string
   */
  protected $template;

  /**
   * 
   * @var string[]
   */
  protected $templatesPath;

  /**
   *
   * @var string[]
   */
  protected $layoutsPath;

  /**
   * @var string|boolean
   */
  protected $layout = false;

  /**
   * @var ViewInterface
   */
  protected $view;

  /**
   * Return a view for this object
   *
   * @param string $type view type
   * @return ViewInterface
   */
  public function getView($type = null)
  {
    if (empty($this->view)) {
      if (is_null($type)) {
        switch (true) {
          case \Simplify::request()->json() :
            $type = View::JSON;
            break;
        }
      }

      $this->view = View::factory($type, $this);
    }

    return $this->view;
  }

  /**
   * (non-PHPdoc)
   * @see RenderableInterface::getLayout()
   */
  public function getLayout()
  {
    $filename = empty($this->layout) ? $this->getLayoutFilename() : $this->layout;

    if ((is_null($filename) && \Simplify::request()->ajax()) || $filename === false) {
      $layout = false;
    }

    elseif (sy_path_is_absolute($filename)) {
      $layout = sy_fix_extension($filename, 'html');

      if (!file_exists($layout)) {
        throw new \Simplify\ViewException("Layout not found: file not found: <b>{$layout}</b>");
      }
    }
    
    else {
      $layout = $filename;
    }

    return $layout;
  }

  public function getLayoutFilename()
  {
    return $this->layout;
  }

  /**
   * Get a list of paths where layouts can be found
   *
   * @return string|string[] array of file paths
   */
  public function getLayoutsPath()
  {
    if (empty($this->layoutsPath)) {
      $this->layoutsPath = array_reverse(\Simplify::config()->get('templates:path'));
      foreach ($this->layoutsPath as &$value) {
        $value = \Simplify::config()->resolveReferences($value);
      }
    }
    return $this->layoutsPath;
  }
  
  /**
   * 
   * @param array $path
   */
  public function setLayoutsPath($path)
  {
    $this->layoutsPath = $path;
  }

  /**
   * (non-PHPdoc)
   * @see RenderableInterface::getTemplate()
   */
  public function getTemplate()
  {
    $filename = empty($this->template) ?  $this->getTemplateFilename() : $this->template;

    if ($filename === false) {
      $template = false;
    }

    elseif (empty($filename)) {
      throw new \Simplify\ViewException('Template not set');
    }

    else {
      $template = $filename;
    }
    
    return $template;
  }

  /**
   * Get the template filename
   *
   * @return string
   */
  public function getTemplateFilename()
  {
    return $this->template;
  }

  /**
   * Get a list of paths where templates can be found
   *
   * @return string|string[] array of file paths
   */
  public function getTemplatesPath()
  {
    if (empty($this->templatesPath)) {
      $this->templatesPath = \Simplify::config()->get('templates:path');
      foreach ($this->templatesPath as &$value) {
        $value = \Simplify::config()->resolveReferences($value);
      }
    }
    return $this->templatesPath;
  }

  /**
   * 
   * @param array $path
   */
  public function setTemplatesPath($path)
  {
    $this->templatesPath = $path;
  }

  /**
   * (non-PHPdoc)
   * @see RenderableInterface::setTemplate()
   */
  public function setTemplate($template)
  {
    $this->template = $template;
  }

  /**
   * (non-PHPdoc)
   * @see RenderableInterface::setLayout()
   */
  public function setLayout($layout)
  {
    $this->layout = $layout;
  }

  /**
   * Call render and return the rendered view
   *
   * @return string
   */
  public function __toString()
  {
    try {
      $output = $this->getView()->render();
    }
    catch (\Exception $e) {
      sy_exception_handler($e);

      $output = '';
    }

    return $output;
  }

}
