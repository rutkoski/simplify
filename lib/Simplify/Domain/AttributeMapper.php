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
 * Default domain data mapper
 *
 */
class Simplify_Domain_AttributeMapper implements Simplify_Domain_AttributeMapperInterface
{

  /**
   * Domain attribute model
   * 
   * @var Simplify_Domain_Model_Attribute
   */
  public $model;

  /**
   * Data type
   * 
   * @var string
   */
  public $type;

  /**
   * Constructor
   * 
   * @param Simplify_Domain_AttributeModel $model domain attribute model
   * @param string $type data type
   */
  public function __construct(Simplify_Domain_AttributeModel $model, $type)
  {
    $this->type = $type;
    $this->model = $model;
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Domain_DataMapperInterface::inflate()
   */
  public function inflate($value)
  {
    switch ($this->type) {
      case Simplify_Domain_AttributeType::TEXT :
        $value = (string) $value;
        break;

      case Simplify_Domain_AttributeType::INTEGER :
        $value = intval($value);
        break;

      case Simplify_Domain_AttributeType::DECIMAL :
      case Simplify_Domain_AttributeType::FLOAT :
        $value = floatval($value);
        break;

      case Simplify_Domain_AttributeType::DATE :
      case Simplify_Domain_AttributeType::TIME :
      case Simplify_Domain_AttributeType::DATETIME :
      case Simplify_Domain_AttributeType::TIMESTAMP :
        $value = new DateTime($value);
        break;

      case Simplify_Domain_AttributeType::BOOLEAN :
        $value = (bool) intval($value);
        break;
    }

    return $value;
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Domain_DataMapperInterface::deflate()
   */
  public function deflate($value)
  {
    switch ($this->type) {
      case Simplify_Domain_AttributeType::TEXT :
        $value = (string) $value;
        break;

      case Simplify_Domain_AttributeType::INTEGER :
        $value = intval($value);
        break;

      case Simplify_Domain_AttributeType::DECIMAL :
      case Simplify_Domain_AttributeType::FLOAT :
        $value = floatval($value);
        break;

      case Simplify_Domain_AttributeType::DATE :
        $value = $value->format('Y-m-d');
        break;

      case Simplify_Domain_AttributeType::TIME :
        $value = $value->format('H:i:s');
        break;

      case Simplify_Domain_AttributeType::DATETIME :
      case Simplify_Domain_AttributeType::TIMESTAMP :
        $value = $value->format('Y-m-d H:i:s');
        break;

      case Simplify_Domain_AttributeType::BOOLEAN :
        $value = $value ? 1 : 0;
        break;
    }

    return $value;
  }

}
