<?php

class Simplify_Db_Constraint
{

  const RESTRICT = 'RESTRICT';

  const CASCADE = 'CASCADE';

  const SET_NULL = 'SET NULL';

  const NO_ACTION = 'NO ACTION';

  public $table;

  public $column;

  public $referencedTable;

  public $referencedColumn;

  public $onUpdate = self::NO_ACTION;

  public $onDelete = self::NO_ACTION;

}