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
 * Interface that represents the result of a SQL query
 *
 * @author Rodrigo Rutkoski Rodrigues <rutkoski@gmail.com>
 */
interface QueryResultInterface
{

  /**
   * Get the query associated with this result
   *
   * @return string
   */
  public function query();

  /**
   * Free memory associated with this query result
   *
   * @return void
   */
  public function free();

  /**
   * Number of rows in the query result in a SELECT or number of affected rows in a INSERT, UPDATE or DELETE
   *
   * @return integer
   */
  public function numRows();

  /**
   * Number of columns in the query result
   *
   * @return integer
   */
  public function numCols();

  /**
   * Column names in the query result
   *
   * @return array
   */
  public function columnNames();

  /**
   * Fetch the first column of the first row in the query result
   *
   * @return mixed
   */
  public function fetchOne();

  /**
   * Fetch the {$n}th row in the query result
   *
   * @return array
   */
  public function fetchRow($n = null);

  /**
   * Fetch the {$n}th column in the query result
   *
   * @return array
   */
  public function fetchCol($n = null);

  /**
   * Fetch the whole result set of the query result
   *
   * @return array
   */
  public function fetchAll();

}
