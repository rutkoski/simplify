<?php

namespace Simplify\Db\Schema;

class Column
{

  public $field;

  public $type;

  public $size;

  public $null;

  public $default;

  public $unsigned;

  public $zerofill;

  public $autoIncrement;

  public function __construct($field)
  {
    $this->field = $field;
  }

  public function getColumnSql()
  {
    if (!empty($this->zerofill)) {
      $this->unsigned = true;
    }

    $s = array();

    $s[] = "`{$this->field}`";

    if (!empty($this->size)) {
      $s[] = sprintf('%s(%s)', strtoupper($this->type), $this->size);
    }
    else {
      $s[] = strtoupper($this->type);
    }

    if (is_numeric($this->size) && !empty($this->unsigned)) {
      $s[] = 'UNSIGNED';

      if (empty($this->zerofill)) {
        $s[] = 'ZEROFILL';
      }
    }

    if (empty($this->null)) {
      $s[] = 'NOT NULL';
    }
    else {
      $s[] = 'NULL';
    }

    if (!empty($this->default)) {
      $s[] = sprintf('DEFAULT %s', $this->default);
    }
    elseif (is_null($this->default) && $this->null) {
      $s[] = 'DEFAULT NULL';
    }

    if (!empty($this->autoIncrement)) {
      $s[] = 'AUTO_INCREMENT';
    }

    return implode(' ', $s);
  }

}