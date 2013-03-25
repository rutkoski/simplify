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
 * Domain entity
 *
 */
class Simplify_Domain_Entity extends Simplify_Domain_DomObj
{

  /**
   *
   * @return Simplify_Domain_Association
   */
  public function getAssociation($name)
  {
    $model = $this->getModel();

    $model->hasAssociation($name);

    return $model->factoryAssociation($name);
  }

  /**
   *
   * @return Simplify_Domain_Model_Entity
   */
  public function getModel()
  {
    return parent::getModel();
  }

  /**
   * 
   * @return string
   */
  protected function get_id()
  {
    return $this->_get($this->getModel()->getPrimaryKey());
  }

  /**
   * 
   * @return boolean
   */
  protected function get_isNew()
  {
    return empty($this->data[$this->getModel()->getPk()]);
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Data_Holder::_del()
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

  /**
   * (non-PHPdoc)
   * @see Simplify_Data_Holder::_get()
   */
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

  /**
   * (non-PHPdoc)
   * @see Simplify_Data_Holder::_has()
   */
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

  /**
   * (non-PHPdoc)
   * @see Simplify_Data_Holder::_set()
   */
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
