<?php

class BelongsToAssociation extends Association
{

  public function beforeSave(Entity $entity, $name)
  {
    if ($entity->has($name)) {
      $source = $entity->getModel();
      $target = $this->getTargetModel();

      $localKey = $this->getLocalKey($source);

      $foreignKey = $this->getForeignKey($source);

      $row = $entity->{$name};

      RepositoryManager::factory($target)->save($row);

      $entity[$localKey] = $row[$foreignKey];
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

      $data = RepositoryManager::factory($this->getTargetModel())->find(null, $params);

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
      $target = $this->getTargetModel();

      $this->model['localKey'] = Inflector::variablize($target->getName() . '_' . $target->getPrimaryKey());
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

}
