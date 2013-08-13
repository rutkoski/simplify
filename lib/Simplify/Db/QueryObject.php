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
 * Base class for Simplify_Db_QueryObjectInterface implementations
 *
 */
abstract class Simplify_Db_QueryObject implements Simplify_Db_QueryObjectInterface
{

  const SELECT = 1;

  const UPDATE = 2;

  const INSERT = 4;

  const DELETE = 8;

  const ALL = 15;

  const JOIN_INNER = 'INNER JOIN';

  const JOIN_LEFT = 'LEFT JOIN';

  const JOIN_RIGHT = 'RIGHT JOIN';

  const ORDER_ASC = 'ASC';

  const ORDER_DESC = 'DESC';

  protected $query = 1;

  protected $alias;

  protected $fields = array();

  protected $table = array();

  protected $joins = array();

  protected $groupBy = array();

  protected $having = array();

  protected $where = array();

  protected $orderBy = array();

  protected $limit;

  protected $offset;

  protected $data;

  protected $sql;

  protected $__sql = null;

  /**
   *
   * @var Simplify_Db_Database
   */
  protected $dao;

  /**
   * Construct a new query object
   *
   * @param Simplify_Db_Database $dao the database object
   */
  public function __construct($dao)
  {
    $this->dao = $dao;
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Db_QueryObjectInterface::alias()
   */
  public function alias($alias = null)
  {
    if ($this->alias !== $alias) {
      $this->__sql = null;
    }

    if ($alias === true) {
      return $this->alias;
    }

    $this->alias = is_string($alias) ? $alias : null;

    return $this;
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Db_QueryObjectInterface::insert()
   */
  public function insert($table = null, $data = null)
  {
    $this->query = Simplify_Db_QueryObject::INSERT;

    $this->from($table);
    $this->data($data);

    return $this;
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Db_QueryObjectInterface::update()
   */
  public function update($table = null, $data = null, $where = null)
  {
    $this->query = Simplify_Db_QueryObject::UPDATE;

    $this->from($table);
    $this->data($data);
    $this->where($where);

    return $this;
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Db_QueryObjectInterface::delete()
   */
  public function delete($table = null, $where = null)
  {
    $this->query = Simplify_Db_QueryObject::DELETE;

    $this->from($table);
    $this->where($where);

    return $this;
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Db_QueryObjectInterface::data()
   */
  public function data($data = null, $remove = null)
  {
    if ($data === true) {
      return $this->data;
    }
    elseif ($data === false) {
      $this->data = null;
    }
    else {
      $this->data = array_merge((array) $this->data, (array) $data);

      $this->__sql = null;
    }

    return $this;
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Db_QueryObjectInterface::select()
   */
  public function select($fields = null, $remove = null)
  {
    if ($fields === true) {
      return $this->fields;
    }
    elseif ($fields === false) {
      $this->fields = array();
    }
    elseif (!empty($fields)) {
      $this->query = Simplify_Db_QueryObject::SELECT;

      $fields = (array) $fields;

      if ($remove) {
        $this->fields = array_diff($this->fields, $fields);
      }
      else {
        $this->fields = array_unique(array_merge($this->fields, $fields));
      }

      $this->__sql = null;
    }

    return $this;
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Db_QueryObjectInterface::from()
   */
  public function from($table = null, $remove = null)
  {
    if ($table === true) {
      return $this->table;
    }
    elseif ($table === false) {
      $this->table = array();
    }
    elseif (!empty($table)) {
      if (!is_array($table)) {
        $table = array($table);
      }

      if ($remove) {
        $this->table = array_diff($this->table, $table);
      }
      else {
        $this->table = array_unique(array_merge($this->table, $table));
      }

      $this->__sql = null;
    }

    return $this;
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Db_QueryObjectInterface::join()
   */
  public function join($join = null, $type = null, $remove = null)
  {
    if ($join === true) {
      return $this->joins;
    }
    elseif ($join === false) {
      $this->joins = array();
    }
    elseif (is_array($join)) {
      foreach ($join as $_join) {
        $this->join($_join, $type, $remove);
      }
    }
    elseif (!empty($join)) {
      if (empty($type)) {
        $type = Simplify_Db_QueryObject::JOIN_INNER;
      }

      $join = array($type, $join);

      if ($remove) {
        if (($i = array_search($join, $this->joins)) !== false) {
          unset($this->joins[$i]);
        }
      }
      else {
        $this->joins[] = $join;
      }

      $this->__sql = null;
    }

    return $this;
  }

  /**
   *
   * @return Simplify_Db_QueryObjectInterface
   */
  public function leftJoin($join = null, $remove = null)
  {
    return $this->join($join, self::JOIN_LEFT, $remove);
  }

  /**
   *
   * @return Simplify_Db_QueryObjectInterface
   */
  public function rightJoin($join = null, $remove = null)
  {
    return $this->join($join, self::JOIN_RIGHT, $remove);
  }

  /**
   *
   * @return Simplify_Db_QueryObjectInterface
   */
  public function innerJoin($join = null, $remove = null)
  {
    return $this->join($join, self::JOIN_INNER, $remove);
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Db_QueryObjectInterface::groupBy()
   */
  public function groupBy($field = null, $remove = null)
  {
    if ($field === true) {
      return $this->groupBy;
    }
    elseif ($field === false) {
      $this->groupBy = array();
    }
    elseif (!empty($field)) {
      $field = (array) $field;

      if ($remove) {
        $this->groupBy = array_diff($this->groupBy, $field);
      }
      else {
        $this->groupBy = array_unique(array_merge($this->groupBy, $field));
      }

      $this->__sql = null;
    }

    return $this;
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Db_QueryObjectInterface::having()
   */
  public function having($condition = null, $remove = null)
  {
    if ($condition === true) {
      return $this->having;
    }
    elseif ($condition === false) {
      $this->having = array();
    }
    elseif (!empty($condition)) {
      $condition = (array) $condition;

      if ($remove) {
        $this->having = array_diff($this->having, $condition);
      }
      else {
        $this->having = array_unique(array_merge($this->having, $condition));
      }

      $this->__sql = null;
    }

    return $this;
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Db_QueryObjectInterface::where()
   */
  public function where($condition = null, $remove = null)
  {
    if ($condition === true) {
      return $this->where;
    }
    elseif ($condition === false) {
      $this->where = array();
    }
    elseif (!empty($condition)) {
      $condition = array($condition);

      if ($remove) {
        $this->where = array_diff($this->where, $condition);
      }
      else {
        $this->where = array_unique(array_merge($this->where, $condition));
      }

      $this->__sql = null;
    }

    return $this;
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Db_QueryObjectInterface::orderBy()
   */
  public function orderBy($field = null, $direction = null, $remove = null)
  {
    if ($field === true) {
      return $this->orderBy;
    }
    elseif ($field === false) {
      $this->orderBy = array();
    }
    elseif (!empty($field)) {
      if ($remove) {
        if (isset($this->orderBy[$field])) {
          unset($this->orderBy[$field]);
        }
      }
      else {
        $this->orderBy[$field] = $direction;
      }

      $this->__sql = null;
    }

    return $this;
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Db_QueryObjectInterface::limit()
   */
  public function limit($limit = null)
  {
    if ($limit === true) {
      return $this->limit;
    }
    elseif ($limit === false) {
      $this->limit = null;
    }
    else {
      $this->limit = intval($limit);

      $this->__sql = null;
    }

    return $this;
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Db_QueryObjectInterface::offset()
   */
  public function offset($offset = null)
  {
    if ($offset === true) {
      return $this->offset;
    }
    elseif ($offset === false) {
      $this->offset = null;
    }
    else {
      $this->offset = intval($offset);

      $this->__sql = null;
    }

    return $this;
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Db_QueryObjectInterface::sql()
   */
  public function sql($sql = null)
  {
    if ($sql === true) {
      return $this->sql;
    }
    elseif ($sql === false) {
      $this->sql = null;
    }
    else {
      $this->sql = $sql;

      $this->__sql = null;
    }

    return $this;
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Db_QueryObjectInterface::buildQuery()
   */
  public function buildQuery()
  {
    //
  }

  /**
   * Automagically converts the query object to string
   *
   * @return string
   */
  public function __toString()
  {
    return $this->buildQuery();
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Db_QueryObjectInterface::setParams()
   */
  public function setParams($params = null)
  {
    if (!empty($params)) {
      if (isset($params[Simplify_Db_QueryParameters::SQL])) {
        $this->sql($params[Simplify_Db_QueryParameters::SQL]);
      }
      else {
        if (isset($params[Simplify_Db_QueryParameters::SELECT])) {
          call_user_func(array($this, 'select'), $params[Simplify_Db_QueryParameters::SELECT]);
        }

        if (isset($params[Simplify_Db_QueryParameters::FROM])) {
          call_user_func(array($this, 'from'), $params[Simplify_Db_QueryParameters::FROM]);
        }

        if (isset($params[Simplify_Db_QueryParameters::JOIN])) {
          foreach ($params[Simplify_Db_QueryParameters::JOIN] as $join) {
            call_user_func_array(array($this, 'join'), (array) $join);
          }
        }

        if (isset($params[Simplify_Db_QueryParameters::INNER_JOIN])) {
          foreach ((array) $params[Simplify_Db_QueryParameters::INNER_JOIN] as $join) {
            call_user_func_array(array($this, 'innerJoin'), (array) $join);
          }
        }

        if (isset($params[Simplify_Db_QueryParameters::GROUP_BY])) {
          call_user_func(array($this, 'groupBy'), $params[Simplify_Db_QueryParameters::GROUP_BY]);
        }

        if (isset($params[Simplify_Db_QueryParameters::HAVING])) {
          call_user_func(array($this, 'having'), $params[Simplify_Db_QueryParameters::HAVING]);
        }

        if (isset($params[Simplify_Db_QueryParameters::WHERE])) {
          foreach ((array) $params[Simplify_Db_QueryParameters::WHERE] as $where) {
            call_user_func(array($this, 'where'), $where);
          }
        }

        if (isset($params[Simplify_Db_QueryParameters::ORDER_BY])) {
          foreach ((array) $params[Simplify_Db_QueryParameters::ORDER_BY] as $orderBy) {
            call_user_func_array(array($this, 'orderBy'), (array) $orderBy);
          }
        }

        if (isset($params[Simplify_Db_QueryParameters::LIMIT])) {
          $this->limit($params[Simplify_Db_QueryParameters::LIMIT]);
        }

        if (isset($params[Simplify_Db_QueryParameters::OFFSET])) {
          $this->offset($params[Simplify_Db_QueryParameters::OFFSET]);
        }

        if (isset($params[Simplify_Db_QueryParameters::DATA])) {
          call_user_func(array($this, 'data'), $params[Simplify_Db_QueryParameters::DATA]);
        }
      }
    }

    return $this;
  }

  /**
   * Build an INSERT's fields/values part of the query
   *
   * @param array $data associative array of field/value pairs
   * @param string|bool|null $wildcard wildcard used for substitution in prepared statements
   * @return string
   */
  public static function buildInsert($data, $wildcard = null)
  {
    $fields = array();
    $values = array();

    foreach ($data as $k => $v) {
      $fields[] = "`$k`";

      if ($wildcard === false) {
        $values[] = s::db()->quote($v);
      }
      elseif (is_string($wildcard)) {
        $values[] = $wildcard;
      }
      else {
        $values[] = ':' . $k;
      }
    }

    return ' (' . implode(', ', $fields) . ') VALUES (' . implode(', ', $values) . ') ';
  }

  /**
   * Build an UPDATE's fields/values part of the query.
   *
   * @param array $data associative array of field/value pairs
   * @param string|bool|null $wildcard wildcard used for substitution in prepared statements
   * @return string
   */
  public static function buildUpdate($data, $wildcard = null)
  {
    $fields = array();

    foreach ($data as $k => $v) {
      if ($wildcard === false) {
        $v = s::db()->quote($v);
        $fields[] = "`$k` = $v";
      }
      elseif (is_string($wildcard)) {
        $fields[] = "`$k` = $wildcard";
      }
      else {
        $fields[] = "`$k` = :$k";
      }
    }

    return ' ' . implode(', ', $fields) . ' ';
  }

  /**
   * Build an equality expression for a single $values or an IN expression for multiple $values
   *
   * @param string $field the field
   * @param mixed $values single or multiple values for the expression
   * @param boolean $not if TRUE, use != or NOT IN instead of = and IN
   * @return string
   */
  public static function buildIn($field, $values, $not = false)
  {
    if (empty($values)) {
      return ' TRUE ';
    }

    $values = (array) $values;

    foreach ($values as &$value) {
      $value = s::db()->quote($value);
    }

    $s = "`{$field}`";

    $value = implode(', ', $values);

    if (count($values) > 1) {
      $s .= ($not ? " NOT" : '') . " IN ({$value})";
    }
    else {
      $s .= ($not ? " !" : ' ') . "= {$value}";
    }

    return $s;
  }

  /**
   * Test the bit mask for a given query type
   *
   * @return boolean
   */
  protected function accept($query)
  {
    return ($query & $this->query) == $this->query;
  }

}
