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
 * Modified Pre-order Tree Traversal (Mptt) utility class.
 *
 * @author "Rodrigo Rutkoski Rodrigues, <rutkoski@gmail.com>"
 */
class Simplify_Db_MPTT
{

  const AFTER = 'after';

  const BEFORE = 'before';

  const FIRST_CHILD = 'first-child';

  const LAST_CHILD = 'last-child';

  protected $table;

  protected $pk;

  protected $left;

  protected $right;

  protected $parent;

  protected static $instances = array();

  /**
   *
   * @param string $table
   * @param string $pk
   * @param string $parent
   * @param string $left
   * @param string $right
   * @return Mptt
   */
  public static function getInstance($table, $pk = 'id', $parent = 'parent_id', $left = 'left_id', $right = 'right_id')
  {
    if (! isset(self::$instances[$table])) {
      self::$instances[$table] = new Mptt($table, $pk, $parent, $left, $right);
    }

    return self::$instances[$table];
  }

  /**
   * Constructor.
   *
   * @param string $table
   * @param string $pk
   * @param string $parent
   * @param string $left
   * @param string $right
   * @return void
   */
  public function __construct($table, $pk = 'id', $parent = 'parent_id', $left = 'left_id', $right = 'right_id')
  {
    $this->table = $table;
    $this->pk = $pk;
    $this->parent = $parent;
    $this->left = $left;
    $this->right = $right;
  }

  /**
   * Convert flat mptt data to hierarchical (recursive array)
   *
   * @param array $flat flat data
   * @return array
   */
  public function toHierarchicalData($flat, $children = 'children')
  {
    $parents = array();

    $data = array();

    while (count($flat)) {
      $row = array_shift($flat);

      $node_id = $row[$this->pk];
      $parent_id = $row[$this->parent];

      if (! isset($parents[$parent_id])) {
        $data[$node_id] = $row;
        $parents[$node_id] = & $data[$node_id];
      }
      else {
        if (! isset($parents[$parent_id][$children])) {
          $parents[$parent_id][$children] = array();
        }

        $parents[$parent_id][$children][$node_id] = $row;

        $parents[$node_id] = & $parents[$parent_id][$children][$node_id];
      }
    }

    return $data;
  }

  /**
   * Rebuild the tree using the parent id
   *
   * @return void
   */
  public function rebuild($orderBy = null)
  {
    try {
      $this->lock();
      $this->_rebuild($orderBy);
    }
    catch (Simplify_Db_MpttException $e) {
      $this->unlock();

      throw $e;
    }

    $this->unlock();
  }

  /**
   *
   * @param array $params
   * @return Simplify_Db_QueryObjectInterface
   */
  public function query()
  {
    $q = Simplify_Db_Database::getInstance()->query()
      ->from("{$this->table} AS node")
      ->from("{$this->table} AS parent")
      ->select('node.*')
      ->select("(COUNT(parent.{$this->pk}) - 1) AS depth")
      ->where("node.{$this->left} BETWEEN parent.{$this->left} AND parent.{$this->right}")
      ->groupBy("node.{$this->pk}")
      ->orderBy("node.{$this->left}");

    return $q;
  }

  public function findAllQuery($where = null)
  {
    if (! empty($where)) {
      $where = "AND $where";
    }

    $sql = "SELECT node.*, (COUNT(parent.$this->pk) - 1) AS depth
      FROM $this->table AS node, $this->table AS parent
      WHERE node.$this->left BETWEEN parent.$this->left AND parent.$this->right $where
      GROUP BY node.$this->pk ORDER BY node.$this->left
    ";

    return $sql;
  }

  public function findSubtreeQuery()
  {
    $sql = "SELECT node.*, (COUNT(parent.$this->pk) - (sub_tree.depth + 1)) AS depth
      FROM $this->table AS node, $this->table AS parent, $this->table AS sub_parent,
        (
          SELECT node.$this->pk, (COUNT(parent.$this->pk) - 1) AS depth
          FROM $this->table AS node, $this->table AS parent
          WHERE node.$this->left BETWEEN parent.$this->left AND parent.$this->right AND node.$this->pk = ?
          GROUP BY node.$this->pk ORDER BY node.$this->left
        ) AS sub_tree
      WHERE node.$this->left BETWEEN parent.$this->left AND parent.$this->right
        AND node.$this->left BETWEEN sub_parent.$this->left AND sub_parent.$this->right
        AND sub_parent.$this->pk = sub_tree.$this->pk
      GROUP BY node.$this->pk
      ORDER BY node.$this->left
    ";

    return $sql;
  }

  /**
   * Remove a node and move it's children to it's parent
   *
   * @param int $id node id
   * @return null
   */
  public function remove($id)
  {
    try {
      if ($id == 0) {
        throw new Simplify_Db_MpttException("Cannot remove id 0");
      }

      $this->lock();

      $dao = Simplify_Db_Database::getInstance();

      $data = $dao->query()->from($this->table)
        ->select("$this->left, $this->right, $this->right - $this->left + 1 AS width, $this->parent")
        ->where("$this->pk = ?")->execute($id)->fetchRow();

      if (empty($data)) {
        throw new Simplify_Db_MpttException("Row not found in table <b>$this->table</b> where <b>$this->pk</b> = <b>$id</b>");
      }

      $left_id = (int) sy_get_param($data, $this->left);
      $right_id = (int) sy_get_param($data, $this->right);
      $width = (int) sy_get_param($data, 'width');
      $parent_id = (int) sy_get_param($data, $this->parent, '0');

      $dao->query("DELETE FROM $this->table WHERE $this->left = ?")->execute($left_id);
      $dao->query("UPDATE $this->table SET $this->parent = ? WHERE $this->parent = ?")->execute($parent_id, $id);
      $dao->query("UPDATE $this->table SET $this->right = $this->right - 1, $this->left = $this->left - 1 WHERE $this->left BETWEEN ? AND ?")->execute($left_id, $right_id);
      $dao->query("UPDATE $this->table SET $this->right = $this->right - 2 WHERE $this->right > ?")->execute($right_id);
      $dao->query("UPDATE $this->table SET $this->left = $this->left - 2 WHERE $this->left > ?")->execute($right_id);
    }
    catch (Simplify_Db_MpttException $e) {
      $this->unlock();

      throw $e;
    }

    $this->unlock();
  }

  /**
   * Remove a node and all of it's children
   *
   * @param int $id node id
   * @return null
   */
  public function removeBranch($id)
  {
    try {
      if ($id == 0) {
        throw new Simplify_Db_MpttException("Cannot remove id 0");
      }

      $this->lock();

      $dao = Simplify_Db_Database::getInstance();

      $data = $dao->query()->from($this->table)->select("$this->left, $this->right, $this->right - $this->left + 1 AS width, $this->parent")->where("$this->pk = ?")->execute($id)->fetchRow();

      if (empty($data)) {
        throw new Simplify_Db_MpttException("Row not found in table <b>$this->table</b> where <b>$this->pk</b> = <b>$id</b>");
      }

      $left_id = (int) sy_get_param($data, $this->left);
      $right_id = (int) sy_get_param($data, $this->right);
      $width = (int) sy_get_param($data, 'width');
      $parent_id = (int) sy_get_param($data, $this->parent, '0');

      $dao->query("DELETE FROM $this->table WHERE $this->left BETWEEN ? AND ?")->execute($left_id, $right_id);
      $dao->query("UPDATE $this->table SET $this->right = $this->right - ? WHERE $this->right > ?")->execute($width, $right_id);
      $dao->query("UPDATE $this->table SET $this->left = $this->left - ? WHERE $this->left > ?")->execute($width, $right_id);
    }
    catch (Simplify_Db_MpttException $e) {
      $this->unlock();

      throw $e;
    }

    $this->unlock();
  }

  /**
   * Count the number of nodes in a node's subtree
   *
   * @param int $id node id
   * @return int node count
   */
  public function numChildren($id)
  {
    $dao = Simplify_Db_Database::getInstance();

    $sql = "SELECT (node.$this->right - node.$this->left - 1) / 2 FROM $this->table AS node WHERE node.$this->pk = ?";

    $num = $dao->query($sql)->execute($id)->fetchOne();

    return (int) $num;
  }

  /**
   * Insert a new node before another node
   *
   * @param array $data node data
   * @param int $id reference node id
   * @return null
   */
  public function before(&$data, $id)
  {
    try {
      if ($id == 0) {
        throw new Simplify_Db_MpttException("\$id must be greather then 0");
      }

      $dao = Simplify_Db_Database::getInstance();

      $this->lock();

      $left_id = $dao->query()->from($this->table)->select($this->left)->where("$this->pk = ?")->execute($id)->fetchOne();

      if (is_null($left_id)) {
        throw new Simplify_Db_MpttException("Row not found in table <b>$this->table</b> where <b>$this->pk</b> = <b>$id</b>");
      }

      $dao->query("UPDATE $this->table SET $this->right = $this->right + 2 WHERE $this->right >= ?")->execute($left_id);
      $dao->query("UPDATE $this->table SET $this->left = $this->left + 2 WHERE $this->left >= ?")->execute($left_id);

      $parent_id = $dao->query()->from($this->table)->select($this->parent)->where("$this->pk = ?")->execute($id)->fetchOne();

      $data[$this->parent] = $parent_id;
      $data[$this->left] = $left_id;
      $data[$this->right] = $left_id + 1;

      $dao->insert($this->table, $data)->execute($data);
    }
    catch (Simplify_Db_MpttException $e) {
      $this->unlock();

      throw $e;
    }

    $this->unlock();
  }

  /**
   * Insert a new node after another node
   *
   * @param array $data node data
   * @param int $id reference node id
   * @return null
   */
  public function after(&$data, $id)
  {
    try {
      if ($id == 0) {
        throw new Simplify_Db_MpttException("\$id must be greather then 0");
      }

      $dao = Simplify_Db_Database::getInstance();

      $this->lock();

      $right_id = $dao->query()->from($this->table)->select($this->right)->where("$this->pk = ?")->execute($id)->fetchOne();

      if (is_null($right_id)) {
        throw new Simplify_Db_MpttException("Row not found in table <b>$this->table</b> where <b>$this->pk</b> = <b>$id</b>");
      }

      $dao->query("UPDATE $this->table SET $this->right = $this->right + 2 WHERE $this->right > ?")->execute($right_id);
      $dao->query("UPDATE $this->table SET $this->left = $this->left + 2 WHERE $this->left > ?")->execute($right_id);

      $parent_id = $dao->query()->from($this->table)->select($this->parent)->where("$this->pk = ?")->execute($id)->fetchOne();

      $data[$this->parent] = $parent_id;
      $data[$this->left] = $right_id + 1;
      $data[$this->right] = $right_id + 2;

      $dao->insert($this->table, $data)->execute($data);
    }
    catch (Simplify_Db_MpttException $e) {
      $this->unlock();

      throw $e;
    }

    $this->unlock();
  }

  /**
   * Insert a new node as the last child of another node
   *
   * @param array $data node data
   * @param int $parent_id parent node id
   * @return null
   */
  public function append(&$data, $parent_id = 0)
  {
    try {
      $dao = Simplify_Db_Database::getInstance();

      $this->lock();

      if (empty($parent_id)) {
        $right_id = $dao->query()->from($this->table)->select("MAX($this->right)")->execute()->fetchOne();
        $right_id = $right_id + 1;
      }
      else {
        $right_id = $dao->query()->from($this->table)->select($this->right)->where("$this->pk = ?")->execute($parent_id)->fetchOne();

        if (is_null($right_id)) {
          throw new Simplify_Db_MpttException("Row not found in table <b>$this->table</b> where <b>$this->pk</b> = <b>$parent_id</b>");
        }

        $dao->query("UPDATE $this->table SET $this->right = $this->right + 2 WHERE $this->right >= ?")->execute($right_id);
        $dao->query("UPDATE $this->table SET $this->left = $this->left + 2 WHERE $this->left >= ?")->execute($right_id);
      }

      $data[$this->parent] = $parent_id;
      $data[$this->left] = $right_id;
      $data[$this->right] = $right_id + 1;

      $dao->insert($this->table, $data)->execute($data);
    }
    catch (Simplify_Db_MpttException $e) {
      $this->unlock();

      throw $e;
    }

    $this->unlock();
  }

  /**
   * Insert a new node as the first child of another node
   *
   * @param array $data node data
   * @param int $parent_id parent node id
   * @return null
   */
  public function prepend(&$data, $parent_id = 0)
  {
    try {
      $this->lock();

      $dao = Simplify_Db_Database::getInstance();

      $left_id = 0;

      if (! empty($parent_id)) {
        $left_id = $dao->query()->from($this->table)->select($this->left)->where("$this->pk = ?")->execute($parent_id)->fetchOne();

        if (is_null($left_id)) {
          throw new Simplify_Db_MpttException("Row not found in table <b>$this->table</b> where <b>$this->pk</b> = <b>$parent_id</b>");
        }
      }

      $dao->query("UPDATE $this->table SET $this->right = $this->right + 2 WHERE $this->right > ?")->execute($left_id);
      $dao->query("UPDATE $this->table SET $this->left = $this->left + 2 WHERE $this->left > ?")->execute($left_id);

      $data[$this->parent] = $parent_id;
      $data[$this->left] = $left_id + 1;
      $data[$this->right] = $left_id + 2;

      $dao->insert($this->table, $data)->execute($data);
    }
    catch (Simplify_Db_MpttException $e) {
      $this->unlock();

      throw $e;
    }

    $this->unlock();
  }

  /**
   * Move a new node to a specific location relative to another node
   *
   * @param int $id node id
   * @param int $ref_id reference node id
   * @param string $pos position
   * @return null
   */
  public function move($id, $ref_id, $pos = self::AFTER)
  {
    if ($id == $ref_id) {
      throw new Simplify_Db_MpttException("Both ids cannot be equal");
    }

    if (($pos == self::BEFORE || $pos == self::AFTER) && $ref_id == 0) {
      throw new Simplify_Db_MpttException("\$id must be greather then 0");
    }

    $dao = Simplify_Db_Database::getInstance();

    $this->lock($this->table, "$this->table a", "$this->table b");

    try {
      $this->__isChild($id, $ref_id);

      $removed = $this->__removeBranch($id);

      $this->__insertBranch($removed, $ref_id, $pos);

      if ($pos == self::FIRST_CHILD || $pos == self::LAST_CHILD) {
        $parent_id = $ref_id;
      } else {
        $parent_id = $dao->query()->from($this->table)->select($this->parent)->where("$this->pk = ?")->execute($ref_id)->fetchOne();
      }

      $dao->update($this->table, array($this->parent => $parent_id), "$this->pk = :$this->pk")->execute(array($this->pk => $id, $this->parent => $parent_id));
    }
    catch (Simplify_Db_MpttException $e) {
      $this->unlock();

      throw $e;
    }

    $this->unlock();
  }

  protected function __isChild($id, $ref_id)
  {
    $dao = Simplify_Db_Database::getInstance();

    $is_child = $dao->query("SELECT b.$this->left BETWEEN a.$this->left AND a.$this->right FROM $this->table a, $this->table b WHERE a.$this->pk = ? AND b.$this->pk = ?")->execute($id, $ref_id)->fetchOne();

    if ($is_child) {
      throw new Simplify_Db_MpttException("Cannot move a node into one of it's children");
    }
  }

  protected function __removeBranch($id)
  {
    $dao = Simplify_Db_Database::getInstance();

    $data = $dao->query()->from($this->table)->select("$this->left, $this->right, $this->right - $this->left + 1 AS width, $this->parent")->where("$this->pk = ?")->execute($id)->fetchRow();

    if (empty($data)) {
      throw new Simplify_Db_MpttException("Row not found in table <b>$this->table</b> where <b>$this->pk</b> = <b>$id</b>");
    }

    $left_id = (int) sy_get_param($data, $this->left);
    $right_id = (int) sy_get_param($data, $this->right);
    $width = (int) sy_get_param($data, 'width');
    $parent_id = (int) sy_get_param($data, $this->parent, '0');

    $ids = $dao->query()->from($this->table)->select($this->pk)->where("$this->left BETWEEN ? AND ?")->execute($left_id, $right_id)->fetchCol();
    $_ids = implode(', ', $ids);

    $dao->query("UPDATE $this->table SET $this->right = $this->right - ? WHERE $this->right > ? AND $this->pk NOT IN ($_ids)")->execute($width, $right_id);
    $dao->query("UPDATE $this->table SET $this->left = $this->left - ? WHERE $this->left > ? AND $this->pk NOT IN ($_ids)")->execute($width, $right_id);

    return array('ids' => $ids, 'width' => $width, 'left' => $left_id, 'right' => $right_id);
  }

  protected function __insertSpace($removed, $ref_id, $pos)
  {
    $dao = Simplify_Db_Database::getInstance();

    $ids = $removed['ids'];
    $width = $removed['width'];
    $oldleft = $removed['left'];
    $oldright = $removed['right'];

    $_ids = implode(', ', $ids);

    switch ($pos) {
      case self::AFTER :
        $right_id = $dao->query()->from($this->table)->select($this->right)->where("$this->pk = ?")->execute($ref_id)->fetchOne();

        if (is_null($right_id)) {
          throw new Simplify_Db_MpttException("Row not found in table <b>$this->table</b> where <b>$this->pk</b> = <b>$id</b>");
        }

        $dao->query("UPDATE $this->table SET $this->right = $this->right + $width WHERE $this->right > ? AND $this->pk NOT IN ($_ids)")->execute($right_id);
        $dao->query("UPDATE $this->table SET $this->left = $this->left + $width WHERE $this->left > ? AND $this->pk NOT IN ($_ids)")->execute($right_id);

        $newleft = $right_id;

        break;

      case self::BEFORE :
        $left_id = $dao->query()->from($this->table)->select($this->left)->where("$this->pk = ?")->execute($ref_id)->fetchOne();

        if (is_null($left_id)) {
          throw new Simplify_Db_MpttException("Row not found in table <b>$this->table</b> where <b>$this->pk</b> = <b>$id</b>");
        }

        $dao->query("UPDATE $this->table SET $this->right = $this->right + $width WHERE $this->right >= ? AND $this->pk NOT IN ($_ids)")->execute($left_id);
        $dao->query("UPDATE $this->table SET $this->left = $this->left + $width WHERE $this->left >= ? AND $this->pk NOT IN ($_ids)")->execute($left_id);

        $newleft = $left_id - 1;

        break;

      case self::FIRST_CHILD :
        $left_id = 0;

        if (! empty($ref_id)) {
          $left_id = $dao->query()->from($this->table)->select($this->left)->where("$this->pk = ?")->execute($ref_id)->fetchOne();

          if (is_null($left_id)) {
            throw new Simplify_Db_MpttException("Row not found in table <b>$this->table</b> where <b>$this->pk</b> = <b>$ref_id</b>");
          }
        }

        $dao->query("UPDATE $this->table SET $this->right = $this->right + $width WHERE $this->right > ? AND $this->pk NOT IN ($_ids)")->execute($left_id);
        $dao->query("UPDATE $this->table SET $this->left = $this->left + $width WHERE $this->left > ? AND $this->pk NOT IN ($_ids)")->execute($left_id);

        $newleft = $left_id;

        break;

      case self::LAST_CHILD :
        if (! empty($ref_id)) {
          $right_id = $dao->query()->from($this->table)->select($this->right)->where("$this->pk = ?")->execute($ref_id)->fetchOne();

          if (is_null($right_id)) {
            throw new Simplify_Db_MpttException("Row not found in table <b>$this->table</b> where <b>$this->pk</b> = <b>$ref_id</b>");
          }

          $newleft = $right_id - 1;

          $dao->query("UPDATE $this->table SET $this->right = $this->right + $width WHERE $this->right >= ? AND $this->pk NOT IN ($_ids)")->execute($right_id);
          $dao->query("UPDATE $this->table SET $this->left = $this->left + $width WHERE $this->left >= ? AND $this->pk NOT IN ($_ids)")->execute($right_id);
        }

        break;
    }

    return $newleft - $oldleft + 1;
  }

  protected function __insertBranch($removed, $ref_id, $pos)
  {
    $dao = Simplify_Db_Database::getInstance();

    $ids = $removed['ids'];
    $width = $removed['width'];
    $oldleft = $removed['left'];
    $oldright = $removed['right'];

    $_ids = implode(', ', $ids);

    $width = $this->__insertSpace($removed, $ref_id, $pos);

    $dao->query("UPDATE $this->table SET $this->left = $this->left + $width, $this->right = $this->right + $width WHERE $this->pk IN ($_ids)")->execute($right_id);
  }

  protected function _rebuild($orderBy = null, $parent_id = 0, $left_id = 0)
  {
    $dao = Simplify_Db_Database::getInstance();

    $right_id = $left_id + 1;

    $children = $dao->query()->from($this->table)->select($this->pk)->where("$this->parent = ?")->orderBy($orderBy)->execute($parent_id)->fetchCol();

    foreach ($children as $child) {
      $right_id = $this->_rebuild($orderBy, $child, $right_id);
    }

    $dao->query("UPDATE $this->table SET $this->left = ?, $this->right = ? WHERE $this->pk = ?")->execute($left_id, $right_id, $parent_id);

    return $right_id + 1;
  }

  protected function lock()
  {
    $tables = func_get_args();

    if (empty($tables)) {
      $tables = array($this->table);
    }

    foreach ($tables as &$table) {
      $table .= ' WRITE';
    }

    $tables = implode(', ', $tables);

    Simplify_Db_Database::getInstance()->query("LOCK TABLES $tables")->executeRaw();
  }

  protected function unlock()
  {
    Simplify_Db_Database::getInstance()->query("UNLOCK TABLES")->executeRaw();
  }

}

class Simplify_Db_MpttException extends Exception {}
