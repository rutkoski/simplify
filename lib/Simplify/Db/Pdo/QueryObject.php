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
 * PDO Query Object
 *
 */
class Simplify_Db_Pdo_QueryObject extends Simplify_Db_QueryObject
{

  /**
   *
   * @var PDOStatement
   */
  public $stmt;

  /**
   *
   * @var string
   */
  public $lastQuery;

  /**
   *
   * @var Simplify_Db_QueryResult
   */
  public $lastResult;

  /**
   *
   * @return MDB2_Driver_Common
   */
  public function db()
  {
    return $this->dao->db();
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Db_QueryObjectInterface::execute()
   */
  public function execute($data = null)
  {
    $query = trim($this->buildQuery());

    if (empty($this->stmt) || $this->lastQuery != $query) {
      $this->stmt = $this->db()->prepare($query);

      $this->lastQuery = $query;
    }

    if (func_num_args() == 0) {
      $data = $this->data;
    } elseif (!is_null($data) && !is_array($data) && func_num_args() > 1) {
      $data = func_get_args();
    }

    $this->lastResult = new Simplify_Db_Pdo_QueryResult($this->stmt, $query, $data, $this->limit, $this->offset);

    return $this->lastResult;
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Db_QueryObjectInterface::executeRaw()
   */
  public function executeRaw()
  {
    $query = $this->buildQuery();

    $this->stmt = $this->db()->query($query);

    $this->lastQuery = $query;

    $this->lastResult = new Simplify_Db_Pdo_QueryResult($this->stmt, $query);

    return $this->lastResult;
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Db_QueryObject::buildQuery()
   */
  public function buildQuery()
  {
    if (!$this->__sql) {
      $sql = $this->sql;

      if (empty($sql)) {
        if ($this->accept(Simplify_Db_QueryObject::SELECT)) {
          /**
           *
           * fields
           *
           */
          $fields = $this->fields;

          if (empty($fields)) {
            $fields = '*';
          }
          else {
            foreach ($fields as &$field) {
              if ($field instanceof Simplify_Db_QueryObjectInterface) {
                $field = $field->buildQuery();
              }
            }

            $fields = implode(', ', $fields);
          }

          $sql = "SELECT $fields FROM ";
        }
        elseif ($this->accept(Simplify_Db_QueryObject::INSERT)) {
          $sql = "INSERT INTO ";
        }
        elseif ($this->accept(Simplify_Db_QueryObject::UPDATE)) {
          $sql = "UPDATE ";
        }
        elseif ($this->accept(Simplify_Db_QueryObject::DELETE)) {
          $sql = "DELETE FROM ";
        }

        /**
         *
         * tables
         *
         */
        if (empty($this->table)) {
          throw new Exception('No table set for query');
        }
        else {
          $tables = $this->table;

          foreach ($tables as &$table) {
            if ($table instanceof Simplify_Db_QueryObjectInterface) {
              $table = $table->buildQuery();
            }
          }

          $table = implode(', ', $tables);
        }

        $sql .= "$table ";

        if ($this->accept(Simplify_Db_QueryObject::UPDATE) && !empty($this->data)) {
          $sql .= 'SET ' . Simplify_Db_QueryObject::buildUpdate($this->data) . ' ';
        }
        elseif ($this->accept(Simplify_Db_QueryObject::INSERT) && !empty($this->data)) {
          $sql .= Simplify_Db_QueryObject::buildInsert($this->data) . ' ';
        }

        if ($this->accept(Simplify_Db_QueryObject::SELECT)) {
          /**
           *
           * joins
           *
           */
          if (!empty($this->joins)) {
            $joins = $this->joins;

            foreach ($joins as &$join) {
              if ($join[1] instanceof Simplify_Db_QueryObjectInterface) {
                $join[1] = $join->buildQuery();
              }

              $join = implode(' ', $join);
            }

            $sql .= implode(' ', $joins) . ' ';
          }
        }

        if ($this->accept(Simplify_Db_QueryObject::ALL ^ Simplify_Db_QueryObject::INSERT)) {
          /**
           *
           * where
           *
           */
          if (!empty($this->where)) {
            $where = $this->where;

            foreach ($where as &$_where) {
              if ($_where instanceof Simplify_Db_QueryObjectInterface) {
                $_where = $_where->buildQuery();
              }
            }

            $where = Simplify_BoolExpr::parse($where);

            $sql .= "WHERE $where ";
          }
        }

        if ($this->accept(Simplify_Db_QueryObject::SELECT)) {
          /**
           *
           * group by and having
           *
           */
          if (!empty($this->groupBy)) {
            $groupBy = implode(', ', $this->groupBy);

            $sql .= "GROUP BY $groupBy ";

            if (!empty($this->having)) {
              $having = $this->having;

              foreach ($having as &$_having) {
                if ($_having instanceof Simplify_Db_QueryObjectInterface) {
                  $_having = $_having->buildQuery();
                }
              }

              $having = Simplify_BoolExpr::parse($having);

              $sql .= "HAVING $having ";
            }
          }
        }

        if ($this->accept(Simplify_Db_QueryObject::ALL ^ Simplify_Db_QueryObject::INSERT)) {
          /**
           *
           * order by
           *
           */
          if (!empty($this->orderBy)) {
            $orderBy = $this->orderBy;

            $order = array();

            foreach ($orderBy as $field => $direction) {
              $order[] = implode(' ', array($field, $direction));
            }

            $orderBy = implode(', ', $order);

            $sql .= "ORDER BY $orderBy ";
          }
        }

        if (!is_null($this->limit)) {
          $sql .= "LIMIT {$this->limit} ";
        }

        if (!is_null($this->offset)) {
          $sql .= "OFFSET {$this->offset} ";
        }

        if ($this->alias && $this->accept(Simplify_Db_QueryObject::SELECT)) {
          $sql = "({$sql}) {$this->alias}";
        }
      }

      $this->__sql = $sql;
    }

    return $this->__sql;
  }

}
