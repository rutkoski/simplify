<?php

class RepositoryManager
{

  protected $repositories;

  /**
   *
   * @param string|DomObjModel $model
   * @return RepositoryInterface
   */
  public static function factory($model)
  {
    return new Repository($model);
  }

}
