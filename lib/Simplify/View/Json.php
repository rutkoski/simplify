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

namespace Simplify\View;

use Simplify;
use Simplify\View;
use Simplify\RenderableInterface;

/**
 *
 * Outputs data as a JSON string
 *
 */
class Json extends View
{

  /**
   * (non-PHPdoc)
   * @see ViewInterface::render()
   */
  public function render(RenderableInterface $object = null)
  {
    if (empty($object)) {
      $object = $this->object;
    }

    if (Simplify::request()->ajax()) {
      Simplify::response()->header('Content-type: application/json; charset="utf-8"');
    }
    else {
      Simplify::response()->header('Content-Type: text/html; charset=UTF-8');
    }

    $output = json_encode($object);

    if (JSON_ERROR_NONE !== json_last_error()) {
      throw new \Exception(json_last_error_msg());
    }

    return $output;
  }

}
