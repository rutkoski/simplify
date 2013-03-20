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
 * Interface for data validation.
 *
 * @author Rodrigo Rutkoski Rodrigues <rutkoski@gmail.com>
 */
interface Simplify_Validation_ValidatorInterface
{

  /**
   * Get error message. This should be set in constructor.
   *
   * @return string
   */
  public function getError();

  /**
   * Validates $value.
   *
   * If validation fails, ValidationException must be thrown.
   *
   * @param mixed $value Value that has to be validated.
   * @return void
   * @throws ValidationException
   */
  public function validate($value);

}
