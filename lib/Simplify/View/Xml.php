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
 */

require_once(APP_DIR.'/includes/lib/XmlSerializer.class.php');

/**
 * Outputs XML
 *
 * @author Rodrigo Rutkoski Rodrigues, <rutkoski@gmail.com>
 * @package Simplify_Kernel_View
 */
class Simplify_View_Xml extends Simplify_View
{

  /**
   * (non-PHPdoc)
   * @see simplify/kernel/view/IView#render($object)
   */
  public function render(Simplify_Renderable_Interface $object = null)
  {
    if (empty($object)) {
      $object = $this->object;
    }

    $serializer = new XmlSerializer();
    $output = $serializer->serialize($object->getAll());

    return $output;
  }

}
