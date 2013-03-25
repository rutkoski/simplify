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

/**
 * 
 * Outputs data as a JSON string
 *
 */
class Simplify_View_Json extends Simplify_View
{

  /**
   * (non-PHPdoc)
   * @see Simplify_ViewInterface::render()
   */
  public function render(Simplify_RenderableInterface $object = null)
  {
    if (empty($object)) {
      $object = $this->object;
    }
    
    if (s::request()->ajax()) {
      s::response()->header('Content-type: application/json; charset="utf-8"');
    }
    else {
      s::response()->header('Content-Type: text/html; charset=UTF-8');
    }
    
    $data = $object->getAll();
    
    array_walk_recursive($data, 'sy_array_map');
    
    $output = json_encode($data);
    
    return $output;
  }

}
