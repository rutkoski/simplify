<?php

/**
 *
 * Model definition:
 *   array( column [, type [, table ]] )
 *   array( 'column' => 'columnName', 'type' => 'columnType', 'table' => 'tableName' )
 *
 * - column name is either index 'column' or 0
 * - column type is either index 'type' or 1
 * - table name is either index 'table' or 2
 *
 */
class Simplify_Domain_Model_Attribute
{

  public $model;

  public function __construct($model = null)
  {
    if ($model instanceof Attribute) {
      $model = $model->model;
    }

    $this->model = (array) $model;
  }

  public function getColumn()
  {
    return sy_get_param($this->model, 0, sy_get_param($this->model, 'column'));
  }

  public function getType()
  {
    return sy_get_param($this->model, 1, sy_get_param($this->model, 'type'));
  }

  public function getTable($default = null)
  {
    return sy_get_param($this->model, 2, sy_get_param($this->model, 'table', $default));
  }

  public function getSql($default = null)
  {
    return sy_get_param($this->model, 'sql');
  }

  public function getFullName($default = null)
  {
    return implode('.', array_filter(array($this->getTable($default), $this->getColumn())));
  }

  public function getMapper()
  {
    if (empty($this->model['mapper'])) {
      $type = $this->getType();

      if (! empty($type) && is_subclass_of($type, 'DataMapper')) {
        $this->model['mapper'] = new $type($this);
      }
      else {
        $this->model['mapper'] = new DefaultDataMapper($this, $type);
      }
    }
    elseif (! ($this->model['mapper'] instanceof DataMapper)) {
      $class = $this->model['mapper'];

      if (is_subclass_of($class, 'DataMapper')) {
        $this->model['mapper'] = new $class($this);
      }
      else {
        throw new DomainException("Mapper <b>$class</b> must extend DataMapper or one of it's subclasses");
      }
    }

    return $this->model['mapper'];
  }

}
