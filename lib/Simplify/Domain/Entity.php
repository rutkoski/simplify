<?php

class Entity extends DomObj
{

  /**
   *
   * @return Association
   */
  public function getAssociation($name)
  {
    $model = $this->getModel();

    $model->hasAssociation($name);

    return $model->factoryAssociation(/*$this, */$name);
  }

  /**
   *
   * @return EntityModel
   */
  public function getModel()
  {
    return parent::getModel();
  }

  protected function get_id()
  {
    return $this->_get($this->getModel()->getPrimaryKey());
  }

  protected function get_isNew()
  {
    return empty($this->data[$this->getModel()->getPk()]);
  }

  /**
   *
   * Simplify_Dictionary methods
   *
   */

  protected function _del($name)
  {
    if ($this->getModel()->hasAttribute($name)) {
      return parent::_del($name);
    }
    elseif ($this->getModel()->hasAssociation($name)) {
      return parent::_del($name);
    }

    throw new DomainException("Field or association <b>$name</b> not found in <b>{$this->getName()}</b>");
  }

  protected function _get($name, $default = null, $flags = 0)
  {
    if ($this->getModel()->hasAttribute($name)) {
      return parent::_get($name, $default, $flags);
    }
    elseif ($this->getModel()->hasAssociation($name)) {
      $this->getAssociation($name)->loadData($this, $name);

      return parent::_get($name, $default, $flags);
    }

    throw new DomainException("Field or association <b>$name</b> not found in <b>{$this->getName()}</b>");
  }

  protected function _has($name, $flags = 0)
  {
    if ($this->getModel()->hasAttribute($name)) {
      return parent::_has($name, $flags);
    }
    elseif ($this->getModel()->hasAssociation($name)) {
      return parent::_has($name, $flags);
    }

    throw new DomainException("Field or association <b>$name</b> not found in <b>{$this->getName()}</b>");
  }

  protected function _set($name, $value)
  {
    if ($this->getModel()->hasAttribute($name)) {
      return parent::_set($name, $value);
    }
    elseif ($this->getModel()->hasAssociation($name)) {
      return parent::_set($name, $value);
    }

    throw new DomainException("Field or association <b>$name</b> not found in <b>{$this->getName()}</b>");
  }

}
