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
 * Offset plugin
 *
 */
class Simplify_Thumb_Plugin_Offset extends Simplify_Thumb_Plugin
{

  /**
   * (non-PHPdoc)
   * @see Simplify_Thumb_Plugin::process()
   */
  protected function process(Simplify_Thumb_Processor $thumb, $top = null, $right = null, $bottom = null, $left = null, $r = 0, $g = 0, $b = 0, $a = 0)
  {
    $thumb->image = Simplify_Thumb_Functions::offset($thumb->image, $top, $right, $bottom, $left, $r, $g, $b, $a);
  }

}
