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
 * Domain Attribute Model
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

  /**
   * Attribute model
   * 
   * @var array
   */
  public $model;

  /**
   * 
   * @param Simplify_Domain_Model_Attribute $model
   */
  public function __construct($model = null)
  {
    if ($model instanceof Simplify_Domain_Model_Attribute) {
      $model = $model->model;
    }
    
    $this->model = (array) $model;
  }

  /**
   * Get column name
   * 
   * @return string
   */
  public function getColumn()
  {
    return sy_get_param($this->model, 0, sy_get_param($this->model, 'column'));
  }

  /**
   * Get attribute type
   * 
   * @return string
   */
  public function getType()
  {
    return sy_get_param($this->model, 1, sy_get_param($this->model, 'type'));
  }

  /**
   * Get table name
   * 
   * @param string $default
   * @return string
   */
  public function getTable($default = null)
  {
    return sy_get_param($this->model, 2, sy_get_param($this->model, 'table', $default));
  }

  /**
   * Get SQL
   * 
   * @param string $default
   * @return string
   */
  public function getSql($default = null)
  {
    return sy_get_param($this->model, 'sql');
  }

  /**
   * Get table and column name
   * 
   * @param string $default
   * @return string
   */
  public function getFullName($default = null)
  {
    return implode('.', array_filter(array($this->getTable($default), $this->getColumn())));
  }

  /**
   * 
   * @throws DomainException
   * @return Simplify_Domain_AttributeMapperInterface
   */
  public function getMapper()
  {
    if (empty($this->model['mapper'])) {
      $type = $this->getType();
      
      if (!empty($type) && $type instanceof Simplify_Domain_AttributeMapperInterface) {
        $this->model['mapper'] = new $type($this);
      }
      else {
        $this->model['mapper'] = new Simplify_Domain_AttributeMapper($this, $type);
      }
    }
    elseif (!($this->model['mapper'] instanceof Simplify_Domain_AttributeMapperInterface)) {
      $class = $this->model['mapper'];
      
      if ($class instanceof Simplify_Domain_AttributeMapperInterface) {
        $this->model['mapper'] = new $class($this);
      }
      else {
        throw new DomainException("Mapper <b>$class</b> must implement Simplify_Domain_AttributeMapperInterface");
      }
    }
    
    return $this->model['mapper'];
  }

}
