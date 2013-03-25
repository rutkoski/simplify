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
 * Base class for validation
 *
 */
abstract class Simplify_Validation_AbstractValidation implements Simplify_ValidationInterface
{

  /**
   * Error message
   *
   * @var string
   */
  protected $message;

  /**
   * Constructor
   * 
   * @param string $message validation fail message
   */
  function __construct($message = '')
  {
    $this->message = $message;
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_ValidationInterface::getError()
   */
  public function getError()
  {
    return $this->message;
  }

  /**
   * 
   * @param string $message
   * @throws Simplify_ValidationException
   */
  protected function fail($message = null)
  {
    if (empty($message)) {
      $message = $this->message;
    }
    throw new Simplify_ValidationException(empty($message) ? 'Validation failed' : $message);
  }

}
