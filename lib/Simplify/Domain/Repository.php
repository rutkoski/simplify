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
 * Default domain repository
 *
 */
class Simplify_Domain_Repository implements Simplify_Domain_RepositoryInterface
{

  /**
   *
   * @var Simplify_Domain_Model_DomObj
   */
  public $model;

  /**
   * 
   * @param unknown_type $model
   */
  public function __construct($model)
  {
    if ($model instanceof Simplify_Domain_Entity) {
      $model = $model->model;
    }
    
    $this->model = $model;
  }

  /**
   *
   * @return Simplify_Domain_Model_Entity
   */
  public function getModel()
  {
    return Simplify_Domain::getEntity($this->model);
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Domain_RepositoryInterface::find()
   */
  public function find($id = null, $params = array())
  {
    $model = $this->getModel();
    
    $q = $this->getBasicQuery($params);
    
    $data = (array) sy_get_param((array) $params, 'data');
    
    if ($id) {
      $table = $model->getTable();
      
      $pk = $model->getAttribute($model->getPrimaryKey())->getColumn();
      
      $q->where("{$table}.{$pk} = :{$pk}");
      
      $data[$pk] = $id;
    }
    
    $q->limit(1);
    
    $this->translateQuery($q);
    
    $row = $q->execute($data)->fetchRow();
    
    if ($row === false) {
      throw new Simplify_Domain_RecordNotFoundException('Record not found');
    }
    
    return Simplify_Domain_DomObjMapper::inflate($this->model, $row);
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Domain_RepositoryInterface::findAll()
   */
  public function findAll($params = array())
  {
    $q = $this->getBasicQuery($params);
    
    $this->translateQuery($q);
    
    $data = (array) sy_get_param($params, 'data');
    
    $data = $q->execute($data)->fetchAll();
    
    $objs = new ArrayObject();
    
    foreach ($data as $row) {
      $objs[] = Simplify_Domain_DomObjMapper::inflate($this->model, $row);
    }
    
    return $objs;
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Domain_RepositoryInterface::findCount()
   */
  public function findCount($params = array())
  {
    $model = $this->getModel();
    
    $main = $model->getTable();
    
    $pk = $model->getAttribute($model->getPrimaryKey())->getColumn();
    
    $data = (array) sy_get_param((array) $params, 'data');
    
    $q = s::db()->query()->setParams($params)->from($main)->select("COUNT({$pk}) AS count");
    
    $this->translateQuery($q);
    
    $count = $q->execute($data)->fetchOne('count');
    
    return $count;
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Domain_RepositoryInterface::save()
   */
  public function save(&$data)
  {
    $model = $this->getModel();
    
    $this->triggerAssociationMethod($data, 'beforeSave');
    
    $pk = $model->getPrimaryKey();
    
    if (isset($data[$pk])) {
      $result = $this->update($data);
    }
    else {
      $result = $this->insert($data);
    }
    
    if ($data) {
      $data->commit();
    }
    
    $this->triggerAssociationMethod($data, 'afterSave');
    
    return $result;
  }

  /**
   * Insert data in repository
   * 
   * @param mixed $data
   */
  protected function insert(&$data)
  {
    $this->triggerAssociationMethod($data, 'beforeInsert');
    
    $model = $this->getModel();
    
    $_data = array();
    
    $pk = $model->getAttribute($model->getPrimaryKey())->getColumn();
    
    foreach ($model->getAttributes() as $attribute => $_model) {
      $_attribute = $model->getAttribute($attribute);
      
      $name = $_attribute->getColumn();
      $table = $_attribute->getTable();
      
      if (empty($table)) {
        $table = $model->getTable();
      }
      
      if ($name && (isset($data[$attribute]) || $name == $pk)) {
        $_data[$table][$name] = $data[$attribute];
      }
    }
    
    $main = true;
    
    foreach ($_data as $table => $__data) {
      if ($main) {
        s::db()->insert($table, $__data)->execute($__data);
        
        $data[$model->getPrimaryKey()] = $id = s::db()->lastInsertId();
        
        $main = false;
      }
      else {
        $__data[$pk] = $id;
        
        s::db()->insert($table, $__data)->execute($__data);
      }
    }
    
    $this->triggerAssociationMethod($data, 'afterInsert');
  }

  /**
   * Update data in repository
   * 
   * @param mixed $data
   */
  protected function update(&$data)
  {
    $this->triggerAssociationMethod($data, 'beforeUpdate');
    
    $model = $this->getModel();
    
    $_data = array();
    
    $pk = $model->getAttribute($model->getPrimaryKey())->getColumn();
    
    $id = $data[$model->getPrimaryKey()];
    
    foreach ($model->getAttributes() as $attribute => $_model) {
      $_attribute = $model->getAttribute($attribute);
      
      $name = $_attribute->getColumn();
      $table = $_attribute->getTable();
      
      if (empty($table)) {
        $table = $model->getTable();
      }
      
      if ($name && isset($data[$attribute])) {
        $_data[$table][$name] = $data[$attribute];
      }
    }
    
    $main = true;
    
    foreach ($_data as $table => $__data) {
      if ($main) {
        $main = false;
      }
      else {
        $__data[$pk] = $id;
      }
      
      s::db()->update($table, $__data, "$pk = :$pk")->execute($__data);
    }
    
    $this->triggerAssociationMethod($data, 'afterUpdate');
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Domain_RepositoryInterface::delete()
   */
  public function delete($id = null, $params = array())
  {
    $this->triggerAssociationMethod($params, 'beforeDelete');
    
    $model = $this->getModel();
    
    $data = (array) sy_get_param($params, 'data', array());
    
    $where = (array) sy_get_param($params, 'where', array());
    
    if (!empty($id)) {
      $pk = $model->getAttribute($model->getPrimaryKey())->getColumn();
      
      $where[] = "{$pk} = :{$pk}";
      
      $data[$pk] = $id;
    }
    
    s::db()->delete($model->getTable(), $where)->execute($data);
    
    $this->triggerAssociationMethod($data, 'afterDelete');
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Domain_RepositoryInterface::deleteAll()
   */
  public function deleteAll($params = null)
  {
    $this->triggerAssociationMethod($params, 'beforeDelete');
    
    $model = $this->getModel();
    
    $pk = $model->getAttribute($model->getPrimaryKey())->getColumn();
    
    $data = sy_get_param($params, 'data');
    $where = sy_get_param($params, 'where');
    
    s::db()->delete($model->getTable(), $where)->execute($data);
    
    $this->triggerAssociationMethod($data, 'afterDelete');
  }

  /**
   *
   * @return Simplify_Db_QueryObject
   */
  protected function getBasicQuery($params = null)
  {
    $model = $this->getModel();
    
    $q = s::db()->query()->setParams($params);
    
    $main = $model->getTable();
    
    $q->from($main);
    
    /*$tables = $model->getTables();

    if (count($tables) > 1) {
      $pk = $model->getAttribute($model->getPrimaryKey())->getColumn();

      for ($i = 1; $i < count($tables); $i++) {
        $table = $tables[$i];

        $q->leftJoin("{$table} ON ({$table}.{$pk} = users.{$pk})");
      }
    }*/
    
    foreach ($model->getAttributes() as $name => $_model) {
      $attribute = $model->getAttribute($name);
      
      $q->select($attribute->getSql($main));
    }
    
    return $q;
  }

  /**
   * 
   * @param mixed $data
   * @param string $method_name
   */
  protected function triggerAssociationMethod(&$data, $method_name)
  {
    foreach ($this->getModel()->getAssociations() as $name => $model) {
      if (isset($data[$name])) {
        $assoc = $this->getModel()->factoryAssociation($name);
        
        if (method_exists($assoc, $method_name)) {
          call_user_func(array($assoc, $method_name), $data, $name);
        }
      }
    }
  }

  /**
   * 
   * @param Simplify_Db_QueryObject $q
   */
  protected function translateQuery(Simplify_Db_QueryObject $q)
  {
    $sql = $q->buildQuery();
    
    if (preg_match_all('#\{([a-z]+)(?:\.([a-z]+))?\}#i', $sql, $matches)) {
      foreach ($matches[0] as $k => $match) {
        $entity = $matches[1][$k];
        $name = $matches[2][$k];
        
        $model = Simplify_Domain::getEntity($entity);
        
        if (!empty($name)) {
          if ($model->hasAttribute($name)) {
            $sql = str_replace($match, $model->getAttribute($name)->getFullName($model->getTable()), $sql);
          }
        }
        else {
          $sql = str_replace($match, $model->getTable(), $sql);
        }
      }
      
      $q->sql($sql);
    }
  }

}
