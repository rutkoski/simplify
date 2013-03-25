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
 * Use a regular expression to validate data.
 *
 * @author Rodrigo Rutkoski Rodrigues, <rutkoski@gmail.com>
 * @package Simplify_Kernel_Data_Validation
 */
class Simplify_Validation_RegexValidator extends Simplify_Validation_AbstractValidator
{

  /**
   * Regular expression.
   *
   * @var string
   */
  public $regex;

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
  public function __construct($message, $regex = null)
  {
    parent::__construct($message);

    $this->regex = $regex;
  }

  /**
   * (non-PHPdoc)
   * @see simplify/kernel/data/validation/IValidator#validate($value)
   */
  public function validate($value)
  {
    if (!preg_match($this->regex, $value)) {
      $this->fail();
    }
  }

}