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
 * Base class for IQueryResult implementations.
 *
 * @author Rodrigo Rutkoski Rodrigues <rutkoski@gmail.com>
 */
abstract class Simplify_Db_QueryResult implements Simplify_Db_QueryResultInterface
{

  /**
   * (non-PHPdoc)
   * @see IQueryResult::columnNames()
   */
  public function columnNames()
  {
  }

  /**
   * (non-PHPdoc)
   * @see IQueryResult::fetchAll()
   */
  public function fetchAll()
  {
  }

  /**
   * (non-PHPdoc)
   * @see IQueryResult::fetchCol()
   */
  public function fetchCol($n = null)
  {
  }

  /**
   * (non-PHPdoc)
   * @see IQueryResult::fetchOne()
   */
  public function fetchOne()
  {
  }

  /**
   * (non-PHPdoc)
   * @see IQueryResult::fetchRow()
   */
  public function fetchRow($n = null)
  {
  }

  /**
   * (non-PHPdoc)
   * @see IQueryResult::free()
   */
  public function free()
  {
  }

  /**
   * (non-PHPdoc)
   * @see IQueryResult::numCols()
   */
  public function numCols()
  {
  }

  /**
   * (non-PHPdoc)
   * @see IQueryResult::numRows()
   */
  public function numRows()
  {
  }

}
