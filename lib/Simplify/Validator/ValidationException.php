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
 * Default exception for validation errors.
 *
 * @author Rodrigo Rutkoski Rodrigues <rutkoski@gmail.com>
 */
class Simplify_Validation_ValidationException extends Exception
{

  protected $errors;

  public function getErrors()
  {
    return $this->errors;
  }

  public function __construct($errors = null, $code = 0)
  {
    $this->errors = $errors;

    parent::__construct('Validation Exception', $code);
  }

}
