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
 * Domain Entity Model
 *
 */
class Simplify_Domain_Model_Entity extends Simplify_Domain_Model_DomObj
{

  /**
   * 
   * @var Simplify_Domain_Association[]
   */
  protected $associations = array();

  /**
   * 
   * @param Simplify_Domain_Model_Entity $model
   */
  public function __construct($model = null)
  {
    parent::__construct($model);
  }

  /**
   *
   * @return Simplify_Domain_Entity
   */
  public function factory($data = null)
  {
    $class = $this->getClass();

    if ($class) {
      $obj = new $class($data);
    }
    else {
      $obj = new Simplify_Domain_Entity();
      $obj->model = $this->getName();
      $obj->copyAll($data);
    }

    return $obj;
  }

  /**
   *
   * @return Simplify_Domain_Model_Attribute
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
      $name = Simplify_Inflector::underscore($this->getName() . '_id');

      if (! $this->hasAttribute($name)) {
        throw new DomainException("Could not determine primary key in entity {$this->getName()}");
      }
    }

    return $name;
  }

  /**
   *
   * @return string
   */
  public function getTable()
  {
    $table = $this->getAttribute($this->getPrimaryKey())->getTable();

    if (empty($table)) {
      $table = Simplify_Inflector::tableize($this->getName());
    }

    return $table;
  }

  /**
   *
   * @return Simplify_Domain_Model_Attribute
   */
  public function getAttribute($name)
  {
    if (! $this->hasAttribute($name)) {
      throw new Simplify_Domain_AttributeNotFoundException("Attribute {$name} not found in entity {$this->getName()}");
    }

    if (! ($this->model['attributes'][$name] instanceof Simplify_Domain_Model_Attribute)) {
      $this->model['attributes'][$name] = new Simplify_Domain_Model_Attribute($this->model['attributes'][$name]);
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

    throw new Simplify_Domain_AttributeNotFoundException('Attribute not found in entity');
  }

  /**
   *
   * @return Simplify_Domain_Model_Attribute[]
   */
  public function getAttributes()
  {
    return sy_get_param($this->model, 'attributes', array());
  }

  /**
   *
   * @return boolean
   */
  public function hasAttribute($name)
  {
    return ! empty($this->model['attributes'][$name]);
  }

  /**
   *
   * @return boolean
   */
  public function hasAssociation($name)
  {
    return isset($this->model['associations'][$name]);
  }

  /**
   *
   * @return Simplify_Domain_Association[]
   */
  public function getAssociations()
  {
    return $this->model['associations'];
  }

  /**
   *
   * @return Simplify_Domain_Association
   */
  public function getAssociation($name)
  {
    $this->hasAssociation($name);

    return $this->model['associations'][$name];
  }

  /**
   * 
   * @param string $name
   * @throws DomainException
   * @return Simplify_Domain_Association
   */
  public function factoryAssociation($name)
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

      $type = Simplify_Inflector::camelize($type);

      $class = $type . 'Association';

      $this->associations/*[$id]*/[$name] = new $class($model/*$source, $name, $target*/);
    }

    return $this->associations/*[$id]*/[$name];
  }

}
