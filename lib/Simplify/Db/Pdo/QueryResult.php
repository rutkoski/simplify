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
 * PDO Query Result
 *
 */
class Simplify_Db_Pdo_QueryResult extends Simplify_Db_QueryResult
{

  /**
   *
   * @var PDOStatement
   */
  protected $stmt;

  /**
   *
   * @var string
   */
  protected $query;

  /**
   *
   * @var mixed
   */
  protected $data;

  /**
   *
   * @var int
   */
  protected $limit;

  /**
   *
   * @var int
   */
  protected $offset;

  /**
   * Constructs a new PDO query result object
   *
   * @param PDOStatement $stmt
   * @param string $query
   * @param array $data
   * @return void
   */
  public function __construct($stmt, $query, $data = null, $limit = null, $offset = null)
  {
    $this->stmt = $stmt;
    $this->query = $query;
    $this->data = $data;
    $this->limit = $limit;
    $this->offset = $offset;

    /**
     * @TODO tratar numero de parametros em relação aos parametros declarados no statement
     */
    if (! is_null($data)) {}

    if ($this->stmt instanceof PDOStatement) {
      $this->stmt->execute((array) $data);
    }

    Simplify_Db_Pdo_Database::validate($this->stmt, $query, $data);

    Simplify_Db_Database::log(array($query, $data, $limit, $offset));
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Db_QueryResult::query()
   */
  public function query()
  {
    return $this->query;
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Db_QueryResult::free()
   */
  public function free()
  {
    $this->stmt->closeCursor();
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Db_QueryResult::fetchOne()
   */
  public function fetchOne()
  {
    $return = $this->stmt->fetchColumn();
    return $return;
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Db_QueryResult::fetchRow()
   */
  public function fetchRow($n = null)
  {
    $return = is_null($n)
      ? $this->stmt->fetch(PDO::FETCH_ASSOC)
      : $this->stmt->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT, $n);
    return $return;
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Db_QueryResult::fetchCol()
   */
  public function fetchCol($n = null)
  {
    $return = is_null($n)
      ? $this->stmt->fetchAll(PDO::FETCH_COLUMN)
      : $this->stmt->fetchAll(PDO::FETCH_COLUMN, intval($n));
    return $return;
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Db_QueryResult::fetchAll()
   */
  public function fetchAll()
  {
    $return = $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    return $return;
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Db_QueryResult::numRows()
   */
  public function numRows()
  {
    if (is_int($this->stmt) || is_bool($this->stmt)) {
      $return = $this->stmt;
    }
    else {
      $return = $this->stmt->rowCount();
    }

    return $return;
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Db_QueryResult::numCols()
   */
  public function numCols()
  {
    $return = $this->stmt->columnCount();
    return $return;
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Db_QueryResult::columnNames()
   */
  public function columnNames()
  {
    throw new Exception('TODO');
  }

}
