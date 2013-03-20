<?php

class DomainMapper
{

  public static function inflate($model, $data, $obj = null)
  {
    $_model = Domain::getEntity($model);

    $id = $_model->getAttribute($_model->getPrimaryKey());

    $_data = array();
    foreach ($_model->getAttributes() as $name => $__model) {
      $attribute = $_model->getAttribute($name);

      $_data[$name] = self::inflateField($attribute, sy_get_param($data, $attribute->getColumn(), null));
    }

    if (empty($obj)) {
      $obj = $_model->factory($_data);
    }

    if (! empty($id)) {
      $obj->commit();
    }

    return $obj;
  }

  public static function inflateField($model, $value)
  {
    if (! ($model instanceof AttributeModel)) {
      $model = new AttributeModel($model);
    }

    $mapper = $model->getMapper();

    if (! empty($mapper)) {
      $value = $mapper->inflate($value);
    }

    return $value;
  }

  public static function deflateField($model, $value)
  {
    if (! ($model instanceof AttributeModel)) {
      $model = new AttributeModel($model);
    }

    $mapper = $model->getMapper();

    if (! empty($mapper)) {
      $value = $mapper->deflate($value);
    }

    return $value;
  }

}
