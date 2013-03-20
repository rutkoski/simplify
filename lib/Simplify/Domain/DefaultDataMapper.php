<?php

class DefaultDataMapper extends DataMapper
{

  public $type;

  public function __construct(AttributeModel $model, $type)
  {
    $this->type = $type;

    parent::__construct($model);
  }

  public function inflate($value)
  {
    switch ($this->type) {
      case FieldType::TEXT :
        $value = (string) $value;
        break;

      case FieldType::INTEGER :
        $value = intval($value);
        break;

      case FieldType::DECIMAL :
      case FieldType::FLOAT :
        $value = floatval($value);
        break;

      case FieldType::DATE :
      case FieldType::TIME :
      case FieldType::DATETIME :
      case FieldType::TIMESTAMP :
        $value = new DateTime($value);
        break;

      case FieldType::BOOLEAN :
        $value = (bool) intval($value);
        break;
    }

    return $value;
  }

  public function deflate($value)
  {
    switch ($this->type) {
      case FieldType::TEXT :
        $value = (string) $value;
        break;

      case FieldType::INTEGER :
        $value = intval($value);
        break;

      case FieldType::DECIMAL :
      case FieldType::FLOAT :
        $value = floatval($value);
        break;

      case FieldType::DATE :
        $value = $value->format('Y-m-d');
        break;

      case FieldType::TIME :
        $value = $value->format('H:i:s');
        break;

      case FieldType::DATETIME :
      case FieldType::TIMESTAMP :
        $value = $value->format('Y-m-d H:i:s');
        break;

      case FieldType::BOOLEAN :
        $value = $value ? 1 : 0;
        break;
    }

    return $value;
  }

}
