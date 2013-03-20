<?php

class Association
{

  const HAS_ONE = 'hasOne';

  const HAS_MANY = 'hasMany';

  const BELONGS_TO = 'belongsTo';

  const HABTM = 'habtm';

  public $model;

  public function __construct($model = null)
  {
    $this->model = (array) $model;
  }

  /**
   * 
   * @return EntityModel
   */
  public function getTargetModel()
  {
    if (! isset($this->model['target'])) {
      $this->model['target'] = Domain::getEntity(sy_get_param($this->model, 1));
    }

    return $this->model['target'];
  }

}
