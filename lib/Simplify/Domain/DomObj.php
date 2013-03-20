<?php

abstract class DomObj extends DataHolder
{

  /**
   * @var DomObjModel
   */
  public $model;

  /**
   *
   *
   * @return DomObjModel
   */
  public function getModel()
  {
    if (empty($this->model)) {
      $this->model = $this->getName();
    }

    return Domain::getObject($this->model);
  }

  public function getName()
  {
    return get_class($this);//$this->getModel()->getName();
  }

}
