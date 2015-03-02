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

namespace Simplify;

/**
 * 
 * Builds a boolean expression from an array
 *
 */
class BoolExpr
{

  /**
   * Recursively parses an array into a boolean expression.
   * First level of the array uses AND operation if $and is TRUE, OR otherwise.
   * Each sub-level of the array inverts the operation.
   *
   * @return string the expression
   */
  public static function parse($expr, $and = true)
  {
    if (empty($expr)) {
      return null;
    }
    
    if (!is_array($expr)) {
      return $expr;
    }
    
    if (count($expr) == 1) {
      return self::parse($expr[0], !$and);
    }
    
    foreach ($expr as &$v) {
      $x = self::parse($v, !$and);
      
      if (is_array($v) && count($v) > 1) {
        $x = "({$x})";
      }
      
      $v = $x;
    }
    
    return implode($and ? ' AND ' : ' OR ', $expr);
  }

}
