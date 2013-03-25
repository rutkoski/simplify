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
 * @author Rodrigo Rutkoski Rodrigues <rutkoski@gmail.com>
 */

/**
 * 
 * Domain Object Model
 *
 */
class Simplify_Domain_Model_DomObj
{

  /**
   * 
   * @var mixed
   */
  public $model;

  /**
   * Constructor
   * 
   * @param Simplify_Domain_Model_DomObj $model
   */
  public function __construct($model = null)
  {
    if (! empty($model)) {
      if ($model instanceof Simplify_Domain_DomObj) {
        $model = $model->model;
      }

      $this->model = $model;
    }
  }

  /**
   *
   * @return Simplify_Domain_DomObj
   */
  public function factory()
  {
    $obj = new Simplify_Domain_DomObj();
    $obj->model = $this->getName();
    return $obj;
  }

  /**
   *
   * @return string
   */
  public function getName()
  {
    if (! isset($this->model['name'])) {
      $name = get_class($this);
      $name = substr($name, 0, strpos($name, 'Model'));
      $this->model['name'] = $name;
    }

    return $this->model['name'];
  }

  /**
   *
   * @return string
   */
  public function getClass()
  {
    $class = null;

    $name = $this->getName();

    if (class_exists($name)) {
      $class = $name;
    }
    elseif (isset($this->model['type'])) {
      $class = $this->model['type'];

      if (! class_exists($class)) {
        throw new DomainException("Domain object class <b>$class</b> not found");
      }

      if (! ($class instanceof Simplify_Domain_DomObj)) {
        throw new DomainException("Class <b>$class</b> must extend Simplify_Domain_DomObj or one of it's subclasses");
      }
    }

    return $class;
  }

}
