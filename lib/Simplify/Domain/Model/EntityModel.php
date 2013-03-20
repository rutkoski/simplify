<?php

class EntityModel extends DomObjModel
{

  protected $associations = array();

  public function __construct($model = null)
  {
    parent::__construct($model);
  }

  /**
   *
   * @return Entity
   */
  public function factory($data = null)
  {
    $class = $this->getClass();

    if ($class) {
      $obj = new $class($data);
    }
    else {
      $obj = new Entity();
      $obj->model = $this->getName();
      $obj->copyAll($data);
    }

    return $obj;
  }

  /**
   *
   * @return AttributeModel
   */
  public function getPrimaryKey()
  {
    if (isset($this->model['pk'])) {
      $name = $this->model['pk'];

      if (! $this->hasAttribute($name)) {
        throw new DomainException("Could not determine primary key in entity {$this->getName()}");
      }
    }
    elseif ($this->hasAttribute('id')) {
      $name = 'id';
    }
    else {
      $name = Inflector::underscore($this->getName() . '_id');

      if (! $this->hasAttribute($name)) {
        throw new DomainException("Could not determine primary key in entity {$this->getName()}");
      }
    }

    return $name;//$this->getAttribute($name);
  }

  /**
   *
   * @return string
   */
  public function getTable()
  {
    $table = $this->getAttribute($this->getPrimaryKey())->getTable();

    if (empty($table)) {
      $table = Inflector::tableize($this->getName());
    }

    return $table;
  }

  /**
   *
   * @return AttributeModel
   */
  public function getAttribute($name)
  {
    if (! $this->hasAttribute($name)) {
      throw new AttributeNotFoundException("Attribute {$name} not found in entity {$this->getName()}");
    }

    if (! ($this->model['attributes'][$name] instanceof AttributeModel)) {
      $this->model['attributes'][$name] = new AttributeModel($this->model['attributes'][$name]);
    }

    return $this->model['attributes'][$name];
  }

  /**
   *
   * @return string
   */
  public function getAttributeName(AttributeModel $attribute)
  {
    foreach ($this->getAttributes() as $name => $model) {
      if ($model === $attribute) {
        return $name;
      }
    }

    throw new AttributeNotFoundException('Attribute not found in entity');
  }

  /**
   *
   * @return array
   */
  public function getAttributes()
  {
    return sy_get_param($this->model, 'attributes', array());
  }

  /**
   *
   * @return void
   */
  public function hasAttribute($name)
  {
    return ! empty($this->model['attributes'][$name]);
  }

  /**
   *
   * @return void
   */
  public function hasAssociation($name)
  {
    return isset($this->model['associations'][$name]);
  }

  /**
   *
   * @return array
   */
  public function getAssociations()
  {
    return $this->model['associations'];
  }

  /**
   *
   * @return AssociationModel
   */
  public function getAssociation($name)
  {
    $this->hasAssociation($name);

    return $this->model['associations'][$name];
  }

  public function factoryAssociation(/*Entity $source, */$name)
  {
    //$id = spl_object_hash($source);

    if (! isset($this->associations/*[$id]*/[$name])) {
      $model = $this->getAssociation($name);

      /*if (isset($model['target'])) {
        $target = $model['target'];
      }
      elseif (isset($model[0])) {
        $target = $model[0];
      }
      else {
        throw new DomainException('Could not determine association target');
      }*/

      $type = sy_get_param($model, 0, sy_get_param($model, 'type'));

      if (empty($type)) {
        throw new DomainException('Could not determine association type');
      }

      $type = Inflector::camelize($type);

      $class = $type . 'Association';

      $this->associations/*[$id]*/[$name] = new $class($model/*$source, $name, $target*/);
    }

    return $this->associations/*[$id]*/[$name];
  }

}
