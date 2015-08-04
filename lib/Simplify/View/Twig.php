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
   *
   * @var \Twig_Environment
   */
  protected static $twig;
  
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
      $view->setTemplatesPath($object->getLayoutsPath());
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

    $twig = self::getTwigInstance();

    $twig->getLoader()->setPaths($path);

    $output = $twig->render($template . '.php', $object->getAll());

    return $output;
  }
  
  public static function getTwigInstance()
  {
    if (!self::$twig) {
      $loader = new \Twig_Loader_Filesystem();
      
      self::$twig = new \Twig_Environment($loader, array(
          //'cache' => \Simplify::config()->get('cache_dir'),
          'autoescape' => false,
      ));
      
      self::loadExtensions(self::$twig);
    }
    
    return self::$twig;
  }
  
  protected static function loadExtensions(\Twig_Environment $twig)
  {
    foreach ((array)\Simplify::config()->get('view:twig:globals') as $name => $value) {
      $twig->addGlobal($name, $value);
    }

    $twig->addGlobal('config', \Simplify::config());
    $twig->addGlobal('request', \Simplify::request());
    $twig->addGlobal('router', \Simplify::router());

    $twig->addFunction(new \Twig_SimpleFunction('makeUrl', array('\Simplify\URL', 'make')));
    $twig->addFunction(new \Twig_SimpleFunction('optionsValue', array('\Amplify\Options', 'value')));

    $twig->addFunction(new \Twig_SimpleFunction('thumb', array('\Simplify\Thumb', 'factory')));
    
    $twig->addFunction(new \Twig_SimpleFunction('asset', array('\Simplify\AssetManager', 'asset')));
    $twig->addFunction(new \Twig_SimpleFunction('assets', array('\Simplify\AssetManager', 'assets')));
    
    $twig->addFilter(new \Twig_SimpleFilter('truncate', 'sy_truncate'));
  }

  public function __toString()
  {
    try {
      return $this->render();
    }
    catch (\Twig_Error_Loader $e) {
      sy_exception(new \Simplify\ViewException($e->getMessage(), 0, $e));
      trigger_error($e);
    }
    catch (\Exception $e) {
      sy_exception($e);
      trigger_error($e);
    }
    
    return '';
  }

}
