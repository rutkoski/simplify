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
 * Use a callback to validate data.
 *
 * @author Rodrigo Rutkoski Rodrigues, <rutkoski@gmail.com>
 * @package Simplify_Kernel_Data_Validation
 */
class Simplify_Validation_StringValidator extends Simplify_Validation_AbstractValidator
{

  const LENGTH = 'length';

  const REQUIRED = 'required';

  /**
   * Rule.
   *
   * @var string
   */
  public $rule;

  /**
   * Validator parameters.
   *
   * @var array
   */
  public $params;

  /**
   * Constructor.
   *
   * @param string|array $callback Valid PHP callback.
   * @param string $message Error message for failing validation.
   * @param array $extraParams Extra parameters for callback.
   * @param integer $valueParamPos In with position does the callback require the
   * value parameter to be.
   * @return void
   */
  public function __construct($message, $rule = null, $params = array())
  {
    parent::__construct($message);

    $this->rule = $rule;
    $this->params = $params;
  }

  /**
   * (non-PHPdoc)
   * @see simplify/kernel/data/validation/IValidator#validate($value)
   */
  public function validate($value)
  {
    $func = 'validate_' . $this->rule;

    if (!method_exists($this, $func)) {
      throw new Exception('Invalid rule name: ' . $this->rule);
    }

    if (!call_user_func(array($this, $func), $value))
      $this->fail();
  }

  protected function validate_required($value)
  {
    return (!empty($value));
  }

  protected function validate_length($value)
  {
    $min = sy_get_param($this->params, 'min', 0);
    $max = sy_get_param($this->params, 'max', false);

    return (strlen($value) >= $min && ($max === false || strlen($value) <= $max));
  }

  /**
   * (non-PHPdoc)
   * @see simplify/kernel/data/validation/IValidator#validate($value)
   */
  public function validate_regex($value, $regex)
  {
    return preg_match($regex, $value);
  }

}
