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
 * @copyright Copyright 2008 Rodrigo Rutkoski Rodrigues
 */

/**
 *
 * Use a callback to validate data
 *
 */
class Simplify_Validation_Callback extends Simplify_Validation_AbstractValidation
{

  /**
   * Callback
   *
   * @var callback
   */
  public $callback;

  /**
   * Extra parameters for callback
   *
   * @var array
   */
  public $extraParams;

  /**
   * Value parameter positon in callback
   *
   * @var integer
   */
  public $valueParamPos;

  /**
   * Constructor
   *
   * @param callback $callback palid PHP callback
   * @param string $message validation fail message
   * @param array $extraParams extra parameters for callback
   * @param integer $valueParamPos the position the callback requires the value parameter to be at
   */
  public function __construct($callback = null, $valueParamPos = 0)
  {
    parent::__construct(null);

    $this->callback = $callback;
    $this->valueParamPos = $valueParamPos;

    $extraParams = func_get_args();
    unset($extraParams[0], $extraParams[1]);
    $this->extraParams = $extraParams;
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_ValidationInterface::validate()
   */
  public function validate($value)
  {
    $args = $this->extraParams;
    array_splice($args, $this->valueParamPos, 0, array($value));
    call_user_func_array($this->callback, $args);
  }

}
