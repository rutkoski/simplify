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
 * Data Mapper Interface
 * 
 * Converts values between repository and domain formats
 *
 */
interface Simplify_Domain_AttributeMapperInterface
{

  /**
   * Convert repository value format to domain value format 
   * 
   * @param mixed $value original value
   * @return mixed converted value
   */
  public function inflate($value);

  /**
   * Convert domain value format to repository value format
   *
   * @param mixed $value original value
   * @return mixed converted value
   */
  public function deflate($value);

}
