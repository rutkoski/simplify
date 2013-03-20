<?php

class HasManyAssociation extends Association
{

  public function afterSave(Entity $entity, $name)
  {
    if ($entity->has($name)) {
      $source = $entity->getModel();
      $target = $this->getTargetModel();

      $localKey = $this->getLocalKey($source);
      $localKeyValue = $entity->get($localKey);

      $foreignKey = $this->getForeignKey($source);

      foreach ($entity->{$name} as $i => &$row) {
        $row[$foreignKey] = $localKeyValue;

        RepositoryManager::factory($target)->save($row);
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

      $foreignTable = $target->getTable();
      $foreignKey = $target->getAttribute($this->getForeignKey($source))->getColumn();

      $params = array(
        'where' => "{$foreignTable}.{$foreignKey} = ?",
        'data' => $localKeyValue
      );

      $data = RepositoryManager::factory($target)->findAll($params);

      $entity->set($name, $data);
      $entity->commit($name);
    }
  }

  public function getJoinExpression(EntityModel $source)
  {
    $target = $this->getTargetModel();

    $foreignTable = $target->getTable();
    $foreignKey = $target->getAttribute($this->getForeignKey($source))->getColumn();

    $localTable = $source->getTable();
    $localKey = $source->getAttribute($this->getLocalKey($source))->getColumn();

    return "{$foreignTable} ON ({$localTable}.{$localKey} = {$foreignTable}.{$foreignKey})";
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

      $this->model['foreignKey'] = Inflector::variablize($source->getName() . '_' . $source->getPrimaryKey());
    }

    return $this->model['foreignKey'];
  }

}
