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
 * Basic implementation of the renderable interface
 * 
 */
abstract class Simplify_Renderable extends Simplify_Dictionary implements Simplify_RenderableInterface
{

  /**
   * @var string
   */
  protected $template;

  /**
   * @var string|boolean
   */
  protected $layout = false;

  /**
   * @var Simplify_ViewInterface
   */
  protected $view;

  /**
   * Return a view for this object
   *
   * @param string $type view type
   * @return Simplify_ViewInterface
   */
  public function getView($type = null)
  {
    if (empty($this->view)) {
      if (is_null($type)) {
        switch (true) {
          case s::request()->json() :
            $type = Simplify_View::JSON;
            break;
          
          default :
            $type = Simplify_View::PHP;
        }
      }
      
      $this->view = Simplify_View::factory($type, $this);
    }
    
    return $this->view;
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_RenderableInterface::getLayout()
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
        if (file_exists($layout))
          break;
      }
    }
    
    if (!file_exists($layout)) {
      throw new Exception("Layout file not found: <b>{$layout}</b>");
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
    return s::config()->get('templates_dir') . '/layouts';
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_RenderableInterface::getTemplate()
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
        if (file_exists($template))
          break;
      }
    }
    
    if (!file_exists($template)) {
      throw new Exception("Template file not found: <b>{$template}</b>");
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
    return s::config()->get('templates_dir');
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_RenderableInterface::setTemplate()
   */
  public function setTemplate($template)
  {
    $this->template = $template;
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_RenderableInterface::setLayout()
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
    catch (Exception $e) {
      sy_exception_handler($e);
      
      $output = '';
    }
    
    return $output;
  }

}
