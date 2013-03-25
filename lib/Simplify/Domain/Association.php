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
 * Domain association
 *
 */
class Simplify_Domain_Association
{

  const HAS_ONE = 'hasOne';

  const HAS_MANY = 'hasMany';

  const BELONGS_TO = 'belongsTo';

  const HABTM = 'habtm';

  /**
   * 
   * @var Simplify_Domain_Model_Association
   */
  public $model;

  /**
   * 
   * @param Simplify_Domain_Model_Association $model
   */
  public function __construct($model = null)
  {
    $this->model = (array) $model;
  }

  /**
   * 
   * @return Simplify_Domain_Model_Entity
   */
  public function getTargetModel()
  {
    if (! isset($this->model['target'])) {
      $this->model['target'] = Simplify_Domain::getEntity(sy_get_param($this->model, 1));
    }

    return $this->model['target'];
  }

}
