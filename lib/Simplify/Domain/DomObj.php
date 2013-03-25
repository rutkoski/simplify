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
 * Abstract domain object
 *
 */
abstract class Simplify_Domain_DomObj extends Simplify_Data_Holder
{

  /**
   * @var Simplify_Domain_Model_DomObj
   */
  public $model;

  /**
   * Get the domain object's model
   *
   * @return Simplify_Domain_Model_DomObj
   */
  public function getModel()
  {
    if (empty($this->model)) {
      $this->model = $this->getName();
    }

    return Simplify_Domain::getObject($this->model);
  }

  /**
   * Get the domain object's name
   * 
   * @return string
   */
  public function getName()
  {
    return get_class($this);
  }

}
