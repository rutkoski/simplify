<?php

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
        $class = Inflector::camelize($name . 'Model');

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
