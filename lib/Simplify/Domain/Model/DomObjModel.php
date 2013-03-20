<?php

class DomObjModel
{

  public $model;

  public function __construct($model = null)
  {
    if (! empty($model)) {
      if ($model instanceof DomObjModel) {
        $model = $model->model;
      }

      $this->model = $model;
    }
  }

  /**
   *
   * @return DomObj
   */
  public function factory()
  {
    $obj = new DomObj();
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

      if (! ($class instanceof DomObj)) {
        throw new DomainException("Class <b>$class</b> must extend DomObj or one of it's subclasses");
      }
    }

    return $class;
  }

}
