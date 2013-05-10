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
    $thumb->image = Simplify_Thumb_Functions::zoomCrop($thumb->image, $width, $height, $gravity);
  }

}
