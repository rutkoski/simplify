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
 * Facade for domain objects
 *
 */
class Simplify_Domain
{

  public static $model = array();

  /**
   *
   * @return EntityModel
   */
  public static function getEntity($model)
  {
    $model = self::getObject($model);

    if (! ($model instanceof Simplify_Domain_Model_Entity)) {
      throw new DomainException("Model is not an instance of Simplify_Domain_Model_Entity");
    }

    return $model;
  }

  /**
   *
   * @return DomObjModel
   */
  public static function getObject($model)
  {
    if ($model instanceof Simplify_Domain_DomObj) {
      if ($model->model) {
        $model = Simplify_Domain::getObject($model->model);
      }
      else {
        $model = Simplify_Domain::getObject($model->getName());
      }
    }
    elseif (! ($model instanceof Simplify_Domain_Model_DomObj)) {
      $name = $model;

      if (! isset(self::$model['objects'][$name])) {
        $class = Simplify_Inflector::camelize($name . 'Model');

        if (class_exists($class)) {
          self::$model['objects'][$name] = new $class;
        }
        else {
          throw new DomainException("Model for object <b>$name</b> not found in domain");
        }
      }

      $model = self::$model['objects'][$name];

      if (! ($model instanceof Simplify_Domain_Model_DomObj)) {
        if (isset($model['type'])) {
          $class = $model['type'];
        }
        else {
          $class = 'EntityModel';
        }

        if (! isset($model['name'])) {
          $model['name'] = $name;
        }

        $model = self::$model['objects'][$name] = new $class($model);

        if (! ($model instanceof Simplify_Domain_Model_DomObj)) {
          throw new DomainException("Class <b>$class</b> must extend Simplify_Domain_Model_DomObj or one of it's subclasses");
        }
      }
    }

    return $model;
  }

}
