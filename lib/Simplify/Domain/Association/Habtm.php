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
 * Domain Has And Belongs To Many Association
 *
 */
class Simplify_Domain_Association_Habtm extends Simplify_Domain_Association
{

  public function afterSave(Entity $entity, $name)
  {
    if ($entity->has($name)) {
      $source = $entity->getModel();
      $target = $this->getTargetModel();

      $localKey = $this->getLocalKey($source);
      $localKeyValue = $entity->get($localKey);

      $foreignKey = $this->getForeignKey($source);

      $associationTable = $this->getAssociationTable($source);
      $associationLocalKey = $this->getAssociationLocalKey($source);
      $associationForeignKey = $this->getAssociationForeignKey($source);

      foreach ($entity->{$name} as $i => &$row) {
        Simplify_Domain_RepositoryManager::factory($target)->save($row);
      }

      s::db()->delete($associationTable, "{$associationTable}.{$associationLocalKey} = ?")->execute($localKeyValue);

      $data = array($associationLocalKey => $localKeyValue);

      foreach ($entity->{$name} as $i => &$row) {
        $data[$associationForeignKey] = $row[$foreignKey];

        s::db()->insert($associationTable, $data)->execute($data);
      }
    }
  }

  public function loadData(Entity $entity, $name)
  {
    if (! $entity->has($name)) {
      $source = $entity->getModel();
      $target = $this->getTargetModel();

      $localKey = $this->getLocalKey($source);

      $localKeyValue = $entity->get($localKey);

      $associationTable = $this->getAssociationTable($source);
      $associationLocalKey = $this->getAssociationLocalKey($source);

      $params = array(
        'innerJoin' => $this->getAssociationJoinExpression($source),
        'where' => "{$associationTable}.{$associationLocalKey} = ?",
        'data' => $localKeyValue
      );

      $data = Simplify_Domain_RepositoryManager::factory($target)->findAll($params);

      $entity->set($name, $data);
      $entity->commit($name);
    }
  }

  public function getJoinExpression(EntityModel $source)
  {
    $target = $this->getTargetModel();

    $associationTable = $this->getAssociationTable($source);
    $associationLocalKey = $this->getAssociationLocalKey($source);
    $associationForeignKey = $this->getAssociationForeignKey($source);

    $foreignTable = $target->getTable();
    $foreignKey = $target->getAttribute($this->getForeignKey($source))->getColumn();

    $localTable = $source->getTable();
    $localKey = $source->getAttribute($this->getLocalKey($source))->getColumn();

    return "{$associationTable} ON ({$localTable}.{$localKey} = {$associationTable}.{$associationLocalKey})"
      . " INNER JOIN {$foreignTable} ON ({$associationTable}.{$associationForeignKey} = {$foreignTable}.{$foreignKey})";
  }

  protected function getAssociationJoinExpression(EntityModel $source)
  {
    $target = $this->getTargetModel();

    $associationTable = $this->getAssociationTable($source);
    $associationForeignKey = $this->getAssociationForeignKey($source);

    $foreignTable = $target->getTable();
    $foreignKey = $target->getAttribute($this->getForeignKey($source))->getColumn();

    return "{$associationTable} ON ({$foreignTable}.{$foreignKey} = {$associationTable}.{$associationForeignKey})";
  }

  protected function getLocalKey(EntityModel $source)
  {
    if (! isset($this->model['localKey'])) {
      $this->model['localKey'] = $source->getPrimaryKey();
    }

    return $this->model['localKey'];
  }

  protected function getForeignKey(EntityModel $source)
  {
    if (! isset($this->model['foreignKey'])) {
      $target = $this->getTargetModel();

      $this->model['foreignKey'] = $target->getPrimaryKey();
    }

    return $this->model['foreignKey'];
  }

  protected function getAssociationLocalKey(EntityModel $source)
  {
    if (! isset($this->model['associationLocalKey'])) {
      $this->model['associationLocalKey'] = $source->getAttribute($this->getLocalKey($source))->getColumn();
    }

    return $this->model['associationLocalKey'];
  }

  protected function getAssociationForeignKey(EntityModel $source)
  {
    if (! isset($this->model['associationForeignKey'])) {
      $target = $this->getTargetModel();

      $this->model['associationForeignKey'] = $target->getAttribute($this->getForeignKey($source))->getColumn();
    }

    return $this->model['associationForeignKey'];
  }

  protected function getAssociationTable(EntityModel $source)
  {
    if (! isset($this->model['associationTable'])) {
      $target = $this->getTargetModel();

      $tables = array(
        Simplify_Inflector::pluralize($target->getName()),
        Simplify_Inflector::pluralize($source->getName())
      );

      sort($tables);

      $this->model['associationTable'] = strtolower(implode('_', $tables));
    }

    return $this->model['associationTable'];
  }

}
