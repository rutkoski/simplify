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
 * Domain Object Mapper
 * 
 * Converts repository between repository data and domain objects
 *
 */
class Simplify_Domain_DomObjMapper
{

  /**
   * Converts repository data to domain objects
   * 
   * @param Simplify_Domain_Model_DomObj $model
   * @param array $data
   * @param Simplify_Domain_DomObj $obj
   * @return Simplify_Domain_DomObj
   */
  public static function inflate($model, $data, $obj = null)
  {
    $_model = Simplify_Domain::getEntity($model);
    
    $id = $_model->getAttribute($_model->getPrimaryKey());
    
    $_data = array();
    foreach ($_model->getAttributes() as $name => $__model) {
      $attribute = $_model->getAttribute($name);
      
      $_data[$name] = self::inflateAttribute($attribute, sy_get_param($data, $attribute->getColumn(), null));
    }
    
    if (empty($obj)) {
      $obj = $_model->factory($_data);
    }
    
    if (!empty($id)) {
      $obj->commit();
    }
    
    return $obj;
  }

  /**
   * Converts repository format value to domain format value
   * 
   * @param Simplify_Domain_Model_Attribute $model
   * @param mixed $value
   * @return mixed
   */
  public static function inflateAttribute($model, $value)
  {
    if (!($model instanceof Simplify_Domain_Model_Attribute)) {
      $model = new Simplify_Domain_Model_Attribute($model);
    }
    
    $mapper = $model->getMapper();
    
    if (!empty($mapper)) {
      $value = $mapper->inflate($value);
    }
    
    return $value;
  }

  /**
   * Converts domain format value to repository format value
   * 
   * @param Simplify_Domain_Model_Attribute $model
   * @param mixed $value
   * @return mixed
   */
  public static function deflateAttribute($model, $value)
  {
    if (!($model instanceof Simplify_Domain_Model_Attribute)) {
      $model = new Simplify_Domain_Model_Attribute($model);
    }
    
    $mapper = $model->getMapper();
    
    if (!empty($mapper)) {
      $value = $mapper->deflate($value);
    }
    
    return $value;
  }

}
