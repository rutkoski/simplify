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

namespace Simplify\Data;

use Iterator;
use Simplify\Data\View;

/**
 *
 * Simplify Data View Iterator
 *
 */
class ViewIterator implements Iterator
{

  protected $data;

  protected $index = 0;

  protected $count;

  /**
   *
   * @var View
   */
  protected $view;

  /**
   *
   * @param array $data
   * @param string $view
   */
  public function __construct(&$data, $view = 'Simplify\Data\View')
  {
    $this->data = &$data;
    
    if (!($view instanceof View)) {
      $view = new $view();
    }
    
    $this->view = $view;
    
    $this->count = count($this->data);
  }

  /**
   * (non-PHPdoc)
   * @see Iterator::rewind()
   */
  public function rewind()
  {
    $this->index = 0;
    $this->view->setData($this->data[$this->index]);
  }

  /**
   * (non-PHPdoc)
   * @see Iterator::current()
   */
  public function current()
  {
    return $this->view;
  }

  /**
   * (non-PHPdoc)
   * @see Iterator::key()
   */
  public function key()
  {
    return $this->index;
  }

  /**
   * (non-PHPdoc)
   * @see Iterator::next()
   */
  public function next()
  {
    $this->index++;
    $this->view->setData($this->data[$this->index]);
  }

  /**
   * (non-PHPdoc)
   * @see Iterator::valid()
   */
  public function valid()
  {
    return ($this->index >= 0 && $this->index < $this->count);
  }

}
