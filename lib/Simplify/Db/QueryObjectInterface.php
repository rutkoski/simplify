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
 * Object-oriented interface for building SQL queries
 *
 * Implementation guidelines:
 * - methods that set parameters must return $this
 * - in case the first parameter is TRUE, methods must RETURN current setting for
 * that parameter
 * - in case the first parameter is FALSE, methods must CLEAR current setting for
 * that parameter
 * - in case the $remove parameter is TRUE, the passed values are removed from
 * the current setting for that parameter
 * - must accept Simplify_Db_QueryObjectInterface objects as parameters
 *
 */
interface Simplify_Db_QueryObjectInterface
{

  /**
   * Set/unset/return the alias for this query.
   *
   * @param string|boolean $alias
   */
  public function alias($alias = null);

  /**
   * Set the query as SELECT and add/remove $fields from the select statement.
   * If $fields is boolean TRUE, the method returns current set fields.
   *
   * @param string|string[]|boolean $fields
   * @param boolean|null $remove
   * @return Simplify_Db_QueryObjectInterface
   */
  public function select($fields = null, $remove = null);

  /**
   * Set the query as INSERT and set the fields/values for the statement.
   *
   * @param string $table the table
   * @param string[string] $data associative array of fields/values
   * @return Simplify_Db_QueryObjectInterface
   */
  public function insert($table = null, $data = null);

  /**
   * Set the query as UPDATE, set the fields/values and the WHERE expression for
   * the statement.
   *
   * @param string $table the table
   * @param string[string] $data associative array of fields/values
   * @param string|array $where where expression
   * @return Simplify_Db_QueryObjectInterface
   */
  public function update($table = null, $data = null, $where = null);

  /**
   * Set the query as DELETE and set the WHERE expression for the statement.
   *
   * @param string $table the table
   * @param string|array $where where expression
   * @return Simplify_Db_QueryObjectInterface
   */
  public function delete($table = null, $where = null);

  /**
   * Set the query as SELECT and add/remove/return the tables for the statement.
   *
   * @param string|string[]|boolean $table the table(s)
   * @param bool|null $remove
   * @return Simplify_Db_QueryObjectInterface
   */
  public function from($table = null, $remove = null);

  /**
   * Add/remove/return the data for the statement.
   *
   * @param string[mixed] $data the data
   * @param bool|null $remove
   * @return Simplify_Db_QueryObjectInterface
   */
  public function data($data = null, $remove = null);

  /**
   * Add/remove a join expression for the statement or return current set joins
   * expressions.
   *
   * @param string $join the join expression
   * @param string $type joing type (INNER JOIN, LEFT JOIN, RIGHT JOIN)
   * @param bool|null $remove
   * @return Simplify_Db_QueryObjectInterface
   */
  public function join($join = null, $type = null, $remove = null);

  /**
   * Add/remove/return one or more GROUP BY expressions for the statement.
   *
   * @param string|string[] $field
   * @param bool|null $remove
   * @return Simplify_Db_QueryObjectInterface
   */
  public function groupBy($field = null, $remove = null);

  /**
   * Add/remove/return one or more HAVING expressions for the statement.
   *
   * @param string $condition
   * @param bool|null $remove
   * @return Simplify_Db_QueryObjectInterface
   */
  public function having($condition = null, $remove = null);

  /**
   * Add/remove/return one or more expressions to the WHERE clause of the
   * statement.
   *
   * @param mixed $condition
   * @param bool|null $remove
   * @return Simplify_Db_QueryObjectInterface
   */
  public function where($condition = null, $remove = null);

  /**
   * Add/remove/return one or more ORDER BY expressions for the statement.
   *
   * @param string $field
   * @param string $direction ASC or DESC
   * @param bool|null $remove
   * @return Simplify_Db_QueryObjectInterface
   */
  public function orderBy($field = null, $direction = null, $remove = null);

  /**
   * Set/unset/return the limit for the statement.
   *
   * @param int|boolean $limit
   * @return Simplify_Db_QueryObjectInterface
   */
  public function limit($limit = null);

  /**
   * Set/unset/return the offset for the statement.
   *
   * @param int|boolean $offset
   * @return Simplify_Db_QueryObjectInterface
   */
  public function offset($offset = null);

  /**
   * Manually set the query.
   *
   * This method unsets all other properties, except for those set by
   * Simplify_Db_QueryObjectInterface::data() and Simplify_Db_QueryObjectInterface::alias().
   *
   * @param string $sql
   * @return Simplify_Db_QueryObjectInterface
   */
  public function sql($sql = null);

  /**
   * Build and return the query.
   *
   * @return string
   */
  public function buildQuery();

  /**
   * Build, prepare, execute and return the result for the query.
   *
   * @param string[mixed] $data the data for the query
   * @return Simplify_Db_QueryResultInterface
   */
  public function execute($data = null);

  /**
   * Build, execute and return the result for the query. Ignores prepared
   * statements.
   *
   * @return Simplify_Db_QueryResultInterface
   */
  public function executeRaw();

  /**
   *
   * @param mixed $params
   * @return Simplify_Db_QueryObjectInterface
   */
  public function setParams($params = null);

}
