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
 * Interface that represents the result of a SQL query
 * 
 * @author Rodrigo Rutkoski Rodrigues <rutkoski@gmail.com>
 */
interface Simplify_Db_QueryResultInterface
{

  /**
   *
   * @return void
   */
  public function free();

  /**
   *
   * @return integer
   */
  public function numRows();

  /**
   *
   * @return integer
   */
  public function numCols();

  /**
   *
   * @return array
   */
  public function columnNames();

  /**
   *
   * @return mixed
   */
  public function fetchOne();

  /**
   *
   * @return array
   */
  public function fetchRow($n = null);

  /**
   *
   * @return array
   */
  public function fetchCol($n = null);

  /**
   *
   * @return array
   */
  public function fetchAll();

}
