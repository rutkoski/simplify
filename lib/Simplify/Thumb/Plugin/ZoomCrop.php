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
 * Zoom crop plugin
 *
 */
class Simplify_Thumb_Plugin_ZoomCrop extends Simplify_Thumb_Plugin
{

  /**
   * (non-PHPdoc)
   * @see Simplify_Thumb_Plugin::process()
   */
  protected function process(Simplify_Thumb_Processor $thumb, $width = null, $height = null, $gravity = Simplify_Thumb::CENTER)
  {
    $image = $thumb->image;
    
    $temp = Simplify_Thumb_Functions::resize($image, $width, $height, true, false);
    
    $w0 = imagesx($temp);
    $h0 = imagesy($temp);
    
    $w1 = empty($width) ? $w0 : $width;
    $h1 = empty($height) ? $h0 : $height;
    
    if ($w0 == $w1 && $h0 == $h1)
      return $this;
    
    switch ($gravity) {
      case Simplify_Thumb::TOP_LEFT :
      case Simplify_Thumb::LEFT :
      case Simplify_Thumb::BOTTOM_LEFT :
        $x = 0;
        break;
      case Simplify_Thumb::TOP_RIGHT :
      case Simplify_Thumb::RIGHT :
      case Simplify_Thumb::BOTTOM_RIGHT :
        $x = $w0 - $w1;
        break;
      case Simplify_Thumb::CENTER :
      default :
        $x = floor($w0 - $w1) / 2;
    }
    
    switch ($gravity) {
      case Simplify_Thumb::TOP_LEFT :
      case Simplify_Thumb::TOP :
      case Simplify_Thumb::TOP_RIGHT :
        $y = 0;
        break;
      case Simplify_Thumb::BOTTOM_LEFT :
      case Simplify_Thumb::BOTTOM :
      case Simplify_Thumb::BOTTOM_RIGHT :
        $y = $h0 - $h1;
        break;
      case Simplify_Thumb::CENTER :
      default :
        $y = floor($h0 - $h1) / 2;
    }
    
    $thumb->image = Simplify_Thumb_Functions::crop($temp, $x, $y, $w1, $h1);
  }

}
