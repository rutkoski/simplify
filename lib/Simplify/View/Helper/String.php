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
 * @author Rodrigo Rutkoski Rodrigues, <rutkoski@gmail.com>
 * @copyright Copyright 2008 Rodrigo Rutkoski Rodrigues
 */

/**
 *
 * 
 *
 */
class Simplify_View_Helper_String extends Simplify_View_Helper
{

  const BREAK_BEFORE = - 1;

  const BREAK_EXACT = 0;

  const BREAK_AFTER = 1;

  /**
   * Truncate string and add ...
   *
   * @param string $string the string
   * @param string $length desired length
   * @param string $trail trailling ... or other string
   * @param int $break use -1 to break before work, 1 to break after word or 0 to break at length
   * @return string truncated string
   */
  public function truncate($string, $length = 80, $trail = '...', $break = Simplify_View_Helper_String::BREAK_BEFORE, $breakstr = ' .,;-:!?')
  {
    if (strlen(utf8_decode($string)) <= $length)
      return $string;

    $string = utf8_decode($string);
    $string = strip_tags($string);

    if ($break == Simplify_View_Helper_String::BREAK_BEFORE) {
      while ($length > 0 && false === strpbrk(substr($string, $length, 1), $breakstr))
        $length --;
    }
    elseif ($break == Simplify_View_Helper_String::BREAK_AFTER) {
      while ($length < strlen($string) && false === strpbrk(substr($string, $length, 1), $breakstr))
        $length ++;
    }

    $string = substr($string, 0, $length);
    $string .= $trail;
    $string = utf8_encode($string);

    return $this->output($string);
  }

}
