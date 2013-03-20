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
 * Interface that represents a DBMS.
 *
 * @author Rodrigo Rutkoski Rodrigues <rutkoski@gmail.com>
 */
interface Simplify_Db_DatabaseInterface
{

  /**
   *
   * @return IDataAccessObject
   */
  public function beginTransaction();

  /**
   *
   * @return IDataAccessObject
   */
  public function commit();

  /**
   *
   * @return IDataAccessObject
   */
  public function rollback();

  /**
   * Factory a query object of the implemented type
   *
   * @return QueryObject
   */
  public function factoryQueryObject();

  /**
   *
   * @return IDataAccessObject
   */
  public function connect();

  /**
   *
   * @return IDataAccessObject
   */
  public function disconnect();

  /**
   *
   * @return mixed
   */
  public function lastInsertId();

  /**
   * Factory an IQueryObject for a SELECT operation.
   *
   * @param string|null $sql
   * @return IQueryObject
   */
  public function query($sql = null);

  /**
   * Factory an IQueryObject for an INSERT operation.
   *
   * @param string $table
   * @param array $data
   * @return IQueryObject
   */
  public function insert($table = null, $data = null);

  /**
   * Factory an IQueryObject for an UPDATE operation.
   *
   * @param string $table
   * @param array $data
   * @param string|array $where
   * @return IQueryObject
   */
  public function update($table = null, $data = null, $where = null);

  /**
   * Factory an IQueryObject for a DELETE operation.
   *
   * @param string $table
   * @param string|array $where
   * @return IQueryObject
   */
  public function delete($table = null, $where = null);

  /**
   *
   * @param mixed $value
   * @param mixed $type
   * @return mixed
   */
  public function quote($value, $type = null);

}
