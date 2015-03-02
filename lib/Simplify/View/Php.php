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

namespace Simplify\View;

/**
 *
 * Basic view using native PHP
 *
 */
class Php extends \Simplify\View
{

  /**
   * (non-PHPdoc)
   * @see \Simplify\ViewInterface::render()
   */
  public function render(\Simplify\RenderableInterface $object = null)
  {
    if (empty($object)) {
      $object = $this->object;
    }

    $template = $object->getTemplate();

    if ($template === false) {
      return '';
    }

    if (sy_path_is_absolute($template)) {
      if (!file_exists($template)) {
        throw new \Exception("Template not found: <b>$template</b>");
      }
    }
    else {
      $filename = $template;

      $path = $object->getTemplatesPath();

      $template = array();
      do {
        $template[] = array_shift($path) . '/' . $filename . '.php';
      } while (count($path) && ! file_exists(end($template)));
      
      if (!file_exists(end($template))) {
        throw new \Exception("Template not found: <b>{$filename}</b><br/><br/>Using path:<br/><b>".implode('</b><br/><b>', $template)."</b>");
      }
      
      $template = end($template);
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
      $view->setTemplatesPath($object->getLayoutsPath());
      $view->setLayout(false);

      $output = $view->render();
    }

    return $output;
  }

  /**
   * Renders the template
   *
   * @param RenderableInterface $object
   * @param string $template
   * @return string
   */
  protected function internalRender(\Simplify\RenderableInterface $object, $template)
  {
    \Simplify::response()->header('Content-Type: text/html; charset=UTF-8');

    extract($object->getAll(), EXTR_REFS);

    ob_start();

    require ($template);

    $output = ob_get_clean();

    return $output;
  }

  protected function includeTemplate($template, $data = null)
  {
    $view = new self();
    $view->setTemplate($template);
    $view->copyAll($data);
    return $view->render();
  }

}
