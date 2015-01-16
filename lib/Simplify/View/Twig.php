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
class Twig extends \Simplify\View
{

  /**
   * (non-PHPdoc)
   * @see Simplify_ViewInterface::render()
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

    /*if (!file_exists($template)) {
      throw new \Exception("Template file not found: <b>$template</b>");
    }*/

    \Simplify::response()->header('Content-Type: text/html; charset=UTF-8');

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
      //$view->setTemplatesPath($object->getLayoutsPath());
      $view->setTemplatesPath($object->getTemplatesPath());
      $view->setLayout(false);

      $output = $view->render();
    }

    return $output;
  }

  /**
   *
   * @param unknown_type $template
   * @param unknown_type $data
   * @return string
   */
  protected function includeTemplate($template, $data = null)
  {
    $view = new self();
    $view->setTemplate($template);
    $view->copyAll($data);
    return $view->render();
  }

  /**
   * Renders the template
   *
   * @param Simplify_RenderableInterface $object
   * @param string $template
   * @return string
   */
  protected function internalRender(\Simplify\RenderableInterface $object, $template)
  {
    $path = $object->getTemplatesPath();

    $path = array_filter($path, function($path) { return is_dir($path); });

    $loader = new \Twig_Loader_Filesystem($path);

    $twig = new \Twig_Environment($loader, array(
      //'cache' => \Simplify::config()->get('cache_dir'),
      'autoescape' => false,
    ));

    $this->loadExtensions($twig);

    $output = $twig->render($template . '.php', $object->getAll());

    return $output;
  }

  protected function loadExtensions(\Twig_Environment $twig)
  {
    foreach ((array)\Simplify::config()->get('view:twig:globals') as $name => $value) {
      $twig->addGlobal($name, $value);
    }

    $twig->addGlobal('config', \Simplify::config());
    $twig->addGlobal('request', \Simplify::request());

    $twig->addFunction(new \Twig_SimpleFunction('makeUrl', array('\Simplify\URL', 'make')));
    $twig->addFunction(new \Twig_SimpleFunction('optionsValue', array('\Amplify\Options', 'value')));
    
    $twig->addFunction(new \Twig_SimpleFunction('scripts', array('\Simplify\AssetManager', 'javascript')));
    $twig->addFunction(new \Twig_SimpleFunction('styles', array('\Simplify\AssetManager', 'style')));
  }

}
