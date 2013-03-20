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
 * Base class for validators.
 *
 * @author Rodrigo Rutkoski Rodrigues, <rutkoski@gmail.com>
 * @package Simplify_Kernel_Data_Validation
 */
abstract class Simplify_Validation_AbstractValidator implements Simplify_Validation_ValidatorInterface
{

  /**
   * Error message.
   *
   * @var string
   */
  protected $message;

  /**
   * Constructor.
   *
   * @return void
   */
  function __construct($message = '')
  {
    $this->message = $message;
  }

  /**
   * (non-PHPdoc)
   * @see simplify/kernel/data/validation/IValidator#getError()
   */
  public function getError()
  {
    return $this->message;
  }

  /**
   * Throws default validation failure exception.
   *
   * @throws ValidationException
   * @return void
   */
  protected function fail($message = null)
  {
    if (empty($message)) {
      $message = $this->message;
    }
    throw new Simplify_Validation_ValidationException(empty($message) ? 'Validation failed' : $message);
  }

}
