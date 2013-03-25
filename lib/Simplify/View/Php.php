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
 * Basic view using native PHP
 *
 */
class Simplify_View_Php extends Simplify_View
{

  /**
   * @var array
   */
  protected static $helpers = array();

  /**
   * (non-PHPdoc)
   * @see Simplify_ViewInterface::render()
   */
  public function render(Simplify_RenderableInterface $object = null)
  {
    if (empty($object)) {
      $object = $this->object;
    }
    
    $template = $object->getTemplate();
    
    if ($template === false)
      return '';
    
    if (!file_exists($template)) {
      throw new Exception("Template file not found: <b>$template</b>");
    }
    
    $output = $this->internalRender($object, $template);
    
    $layout = $object->getLayout();
    
    if ($layout !== false) {
      if (empty($layout)) {
        $layout = $this->getLayout();
      }
      
      $view = new self();
      $view->copyAll($object);
      $view->copyAll($this);
      $view->set('layout_content', $output);
      $view->setTemplate($layout);
      $view->setLayout(false);
      
      $output = $view->render();
    }
    
    return $output;
  }

  /**
   * Renders the template
   * 
   * @param Simplify_RenderableInterface $object
   * @param string $template
   * @return string
   */
  protected function internalRender(Simplify_RenderableInterface $object, $template)
  {
    s::response()->header('Content-Type: text/html; charset=UTF-8');
    
    extract($object->getAll(), EXTR_REFS);
    
    ob_start();
    
    require ($template);
    
    $output = ob_get_clean();
    
    return $output;
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Dictionary::__get()
   */
  public function __get($name)
  {
    if (!isset(self::$helpers[$name])) {
      self::$helpers[$name] = Simplify_View_Helper::factory($name);
    }
    
    return self::$helpers[$name];
  }

}
