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
 * Interface that represents a DBMS
 *
 */
interface DatabaseInterface
{

  /**
   * Begin a transaction
   *
   * @return Simplify\Db\DatabaseInterface
   */
  public function beginTransaction();

  /**
   * Commit a transaction
   *
   * @return Simplify\Db\DatabaseInterface
   */
  public function commit();

  /**
   * Rollback a transaction
   *
   * @return Simplify\Db\DatabaseInterface
   */
  public function rollback();

  /**
   * Factory a query object of the implemented type
   *
   * @return Simplify\Db\QueryObject
   */
  public function factoryQueryObject();

  /**
   * Connect to the datasource
   *
   * @return Simplify\Db\DatabaseInterface
   */
  public function connect();

  /**
   * Disconnect from the datasource
   *
   * @return \Simplify\Db\DatabaseInterface
   */
  public function disconnect();

  /**
   * Get the last inserted id, after an INSERT
   *
   * @return mixed
   */
  public function lastInsertId();

  /**
   * Factory an Simplify\Db\QueryObject for a SELECT operation
   *
   * @param string|null $sql
   * @return \Simplify\Db\QueryObject
   */
  public function query($sql = null);

  /**
   * Factory an Simplify\Db\QueryObject for an INSERT operation
   *
   * @param string $table
   * @param array $data
   * @return \Simplify\Db\QueryObject
   */
  public function insert($table = null, $data = null);

  /**
   * Factory an Simplify\Db\QueryObject for an UPDATE operation
   *
   * @param string $table
   * @param array $data
   * @param string|array $where
   * @return \Simplify\Db\QueryObject
   */
  public function update($table = null, $data = null, $where = null);

  /**
   * Factory an Simplify\Db\QueryObject for a DELETE operation
   *
   * @param string $table
   * @param string|array $where
   * @return \Simplify\Db\QueryObject
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
