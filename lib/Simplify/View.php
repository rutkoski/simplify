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
 * Abstract view
 *
 */
abstract class View extends Renderable implements ViewInterface
{

  const JSON = '\Simplify\View\Json';

  const PHP = '\Simplify\View\Php';

  /**
   *
   * @var RenderableInterface
   */
  protected $object;

  /**
   * Instantiate a view
   *
   * @param string $class
   * @param RenderableInterface $object
   * @return ViewInterface
   */
  public static function factory($class = null, RenderableInterface $object = null)
  {
    if (empty($class)) {
      $class = \Simplify::config()->get('view:default', View::PHP);
    }

    return new $class($object);
  }

  /**
   * Constructor
   *
   * @param RenderableInterface $object
   */
  public function __construct(RenderableInterface $object = null)
  {
    $this->object = $object ? $object : $this;
  }

  public function __toString()
  {
    try {
      return $this->render();
    }
    catch (\Exception $e) {
      trigger_error($e->getMessage());
    }
  }

  /**
   * (non-PHPdoc)
   * @see Dictionary::getAll()
   */
  public function getAll($flags = 0)
  {
    return $this->object->data;
  }

  /**
   * (non-PHPdoc)
   * @see Dictionary::_del()
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
   * @see Dictionary::_get()
   */
  protected function _get($name)
  {
    if (isset($this->object->data[$name])) {
      return $this->object->data[$name];
    }
  }

  /**
   * (non-PHPdoc)
   * @see Dictionary::_has()
   */
  protected function _has($name)
  {
    return isset($this->object->data[$name]);
  }

  /**
   * (non-PHPdoc)
   * @see Dictionary::_set()
   */
  protected function _set($name, $value)
  {
    $this->object->data[$name] = $value;
    return $this;
  }

}
