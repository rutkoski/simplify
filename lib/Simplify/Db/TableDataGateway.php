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
 * Basic Simplify_Db_RepositoryInterface implementation for a single table
 *
 */
class Simplify_Db_TableDataGateway implements Simplify_Db_RepositoryInterface
{

  /**
   *
   * @var string
   */
  public $table;

  /**
   *
   * @var string
   */
  public $pk;

  /**
   *
   * @var string
   */
  public $moveField;

  /**
   *
   * @var array
   */
  protected static $instances = array();

  /**
   * Constructor.
   *
   * return void
   */
  public function __construct($table = null, $pk = null, $orderField = null)
  {
    $this->table = $table;
    $this->pk = $pk;
    $this->orderField = $orderField;
  }

  /**
   *
   * @param string $table
   * @param string $pk
   * @return TableDataGateway
   */
  public static function getInstance($table, $pk = null, $orderField = null)
  {
    if (!isset(self::$instances[$table])) {
      self::$instances[$table] = new self($table, $pk, $orderField);
    }

    return self::$instances[$table];
  }

  public function move($id, $direction, $params = null)
  {
    if (!in_array($direction,
      array('top', 'up', 'down', 'bottom', 'first', 'left', 'right', 'last', 'previous', 'next'))) {
      return;
    }

    $field = $this->orderField;

    $pos = s::db()->query()->setParams($params)->from($this->table)->select($this->pk)->where("$this->pk = $id")->execute()->fetchOne();

    switch ($direction) {
      case 'top' :
      case 'first' :
        $data = s::db()->query()->setParams($params)->from($this->table)->select($this->pk)->where(
          "$field <= $pos AND $this->pk != $id")->execute()->fetchCol();
        $dif = -1;
        $dis = -count($data);
        break;

      case 'up' :
      case 'left' :
      case 'previous' :
        $dif = -1;
        $dis = -1;
        $data = s::db()->query()->setParams($params)->from($this->table)->select($this->pk)->where(
          "($field = $pos - 1 OR $field = $pos) AND $this->pk != $id")->orderBy("$field DESC")->execute()->fetchCol();
        break;

      case 'down' :
      case 'right' :
      case 'next' :
        $dif = 1;
        $dis = 1;
        $data = s::db()->query()->setParams($params)->from($this->table)->select($this->pk)->where(
          "($field = $pos + 1 OR $field = $pos) AND $this->pk != $id")->orderBy("$field ASC")->execute()->fetchCol();
        break;

      case 'bottom' :
      case 'last' :
        $data = s::db()->query()->setParams($params)->from($this->table)->select($this->pk)->where(
          "$field >= $pos AND $this->pk != $id")->execute()->fetchCol();
        $dif = 1;
        $dis = count($data);
        break;
    }

    if (!empty($data)) {
      $sql = "UPDATE $this->table SET $field = GREATEST(0, $field - $dif) WHERE $this->pk IN (" . implode(', ', $data) .
         ")";
      s::db()->query($sql)->execute();
    }

    if ($pos + $dis != $pos) {
      $sql = "UPDATE $this->table SET $field = GREATEST(0, $field + $dis) WHERE $this->pk = $id";
      s::db()->query($sql)->execute();
    }
  }

  /**
   *
   * @param int $offset
   * @param int $limit
   * @return Pager
   */
  public function findPager($params = null)
  {
    $limit = $params['limit'];
    $offset = $params['offset'];

    return new Simplify_Pager($this->findCount($params), $limit, $offset);
  }

  /**
   *
   * @return array
   */
  public function find($id = null, $params = null)
  {
    $query = s::db()->query()->setParams($params)->from($this->table)->where("$this->pk = :$this->pk")->limit(1);

    $data = (array) sy_get_param($params, 'data');
    $data[$this->pk] = $id;

    $result = $query->execute($data)->fetchRow();

    return $result;
  }

  /**
   *
   * @return integer
   */
  public function findCount($params = null)
  {
    $query = s::db()->query()->setParams($params)->from($this->table)->select(false)->limit(false)->offset(false)->select(
      "COUNT($this->pk)");
    $result = $query->execute(sy_get_param($params, 'data'))->fetchOne();
    return intval($result);
  }

  /**
   *
   * @param int $offset
   * @param int $limit
   * @return array
   */
  public function findAll($params = null)
  {
    $query = s::db()->query()->setParams($params)->from($this->table);
    $result = $query->execute(sy_get_param($params, 'data'))->fetchAll();
    return $result;
  }

  /**
   *
   * @return integer
   */
  public function delete($id = null, $params = null)
  {
    $result = s::db()->delete($this->table, "$this->pk = ?")->execute($id);
    return $result->numRows();
  }

  /**
   *
   * @return integer
   */
  public function deleteAll($params = null)
  {
    $result = s::db()->delete($this->table, "$this->pk = ?")->setParams($params)->execute();
    return $result->numRows();
  }

  /**
   *
   * @param array $data
   * @return mixed
   */
  public function save(&$data)
  {
    $id = sy_get_param($data, $this->pk);

    if (empty($id)) {
      return $this->insert($data);
    }
    else {
      return $this->update($data);
    }
  }

  /**
   *
   * @param array $data
   * @return void
   */
  public function insert(&$data)
  {
    s::db()->insert($this->table, $data)->execute($data);
    $data[$this->pk] = s::db()->lastInsertId();
  }

  /**
   *
   * @param mixed $data
   * @return int
   */
  public function update(&$data)
  {
    $result = 0;

    if (count($data) > 1) {
      $result = s::db()->update($this->table, $data, "$this->pk = :$this->pk")->execute($data)->numRows();
    }

    return $result;
  }

}
