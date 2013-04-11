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
 * Base implementation of Simplify_Db_QueryResultInterface
 *
 */
abstract class Simplify_Db_QueryResult implements Simplify_Db_QueryResultInterface
{

  /**
   * (non-PHPdoc)
   * @see Simplify_Db_QueryResultInterface::query()
   */
  public function query()
  {
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Db_QueryResultInterface::columnNames()
   */
  public function columnNames()
  {
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Db_QueryResultInterface::fetchAll()
   */
  public function fetchAll()
  {
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Db_QueryResultInterface::fetchCol()
   */
  public function fetchCol($n = null)
  {
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Db_QueryResultInterface::fetchOne()
   */
  public function fetchOne()
  {
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Db_QueryResultInterface::fetchRow()
   */
  public function fetchRow($n = null)
  {
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Db_QueryResultInterface::free()
   */
  public function free()
  {
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Db_QueryResultInterface::numCols()
   */
  public function numCols()
  {
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Db_QueryResultInterface::numRows()
   */
  public function numRows()
  {
  }

}
