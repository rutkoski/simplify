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

namespace Simplify\Db;

/**
 *
 * Base implementation of Simplify\Db\QueryResultInterface
 *
 */
abstract class QueryResult implements QueryResultInterface
{

  /**
   * (non-PHPdoc)
   * @see Simplify\Db\QueryResultInterface::query()
   */
  public function query()
  {
  }

  /**
   * (non-PHPdoc)
   * @see Simplify\Db\QueryResultInterface::columnNames()
   */
  public function columnNames()
  {
  }

  /**
   * (non-PHPdoc)
   * @see Simplify\Db\QueryResultInterface::fetchAll()
   */
  public function fetchAll()
  {
  }

  /**
   * (non-PHPdoc)
   * @see Simplify\Db\QueryResultInterface::fetchCol()
   */
  public function fetchCol($n = null)
  {
  }

  /**
   * (non-PHPdoc)
   * @see Simplify\Db\QueryResultInterface::fetchOne()
   */
  public function fetchOne()
  {
  }

  /**
   * (non-PHPdoc)
   * @see Simplify\Db\QueryResultInterface::fetchRow()
   */
  public function fetchRow($n = null)
  {
  }

  /**
   * (non-PHPdoc)
   * @see Simplify\Db\QueryResultInterface::free()
   */
  public function free()
  {
  }

  /**
   * (non-PHPdoc)
   * @see Simplify\Db\QueryResultInterface::numCols()
   */
  public function numCols()
  {
  }

  /**
   * (non-PHPdoc)
   * @see Simplify\Db\QueryResultInterface::numRows()
   */
  public function numRows()
  {
  }

}
