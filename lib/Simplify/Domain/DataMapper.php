<?php

abstract class DataMapper
{

  /**
   * @var AttributeModel
   */
  public $model;

  public function __construct(AttributeModel $model)
  {
    $this->model = $model;
  }

  abstract public function inflate($value);

  abstract public function deflate($value);

}
