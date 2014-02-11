<?php

class Simplify_Db_Table
{

  /**
   * Table name
   *
   * @var string
   */
  public $name;

  /**
   *
   * @var array
   */
  public $columns;

  /**
   *
   * @var array
   */
  public $indexes;

  /**
   *
   * @var array
   */
  public $constraints;

  /**
   *
   * @var array
   */
  protected $_schema = false;

  /**
   *
   * @param string $name table name
   * @param string $primaryKey primary key column(s)
   * @param boolean $loadSchema load table schema from database
   */
  public function __construct($name, $primaryKey = null, $loadSchema = true)
  {
    $this->name = $name;

    if (!empty($primaryKey)) {
      $this->setPrimaryKey($primaryKey);
    }

    if ($loadSchema) {
      $this->loadSchema();
    }
  }

  /**
   * Set the primary key column(s)
   *
   * @param string|string[] $primaryKey
   */
  public function setPrimaryKey($primaryKey)
  {
    if (empty($this->indexes['PRIMARY'])) {
      $this->indexes['PRIMARY'] = new Simplify_Db_Index();
    }

    $this->indexes['PRIMARY']->fields = (array) $primaryKey;
  }

  /**
   * Check if field belongs to primary key
   *
   * @param string $field
   * @return boolean
   */
  public function isPrimaryKey($field)
  {
    return !empty($this->indexes['PRIMARY']) && in_array($field, (array) $this->indexes['PRIMARY']->fields);
  }

  /**
   * Get the primary key column(s)
   *
   * @return string|string[]
   */
  public function getPrimaryKey()
  {
    if (empty($this->indexes['PRIMARY'])) {
      return false;
    }

    $pk = $this->indexes['PRIMARY']->fields;

    return is_array($pk) && count($pk) > 1 ? $pk : array_shift($pk);
  }

  public function addColumn(Simplify_Db_Column $column, $field)
  {
    s::db()->query(sprintf("ALTER TABLE `{$this->name}` %s %s", 'ADD COLUMN ', $column->getColumnSql()))->execute();
  }

  public function dropColumn(Simplify_Db_Column $column, $field)
  {
    s::db()->query(sprintf("ALTER TABLE `{$this->name}` %s %s", 'DROP COLUMN ', $field))->execute();
  }

  public function changeColumn(Simplify_Db_Column $column, $field)
  {
    s::db()->query(sprintf("ALTER TABLE `{$this->name}` %s %s", "CHANGE COLUMN `{$field}`", $column->getColumnSql()))->execute();
  }

  public function addIndex($name, Simplify_Db_Index $index)
  {
    s::db()->query(
      sprintf("ALTER TABLE `{$this->name}` ADD %s `%s` (`%s`)", $index->unique ? 'UNIQUE INDEX' : 'INDEX', $name,
        implode('`, `', (array) $index->fields)))->execute();
  }

  public function dropIndex($name)
  {
    s::db()->query(sprintf("ALTER TABLE `{$this->name}` DROP INDEX %s`", $name))->execute();
  }

  public function addForeignKey($name, Simplify_Db_Constraint $constraint)
  {
    s::db()->query(
      sprintf(
        "ALTER TABLE `{$this->name}` ADD FOREIGN KEY `%s` (`%s`) REFERENCES `%s` (`%s`) ON UPDATE %s ON DELETE %s",
        $name, implode('`, `', (array) $constraint->column), $constraint->referencedTable,
        implode('`, `', (array) $constraint->referencedColumn, $constraint->onUpdate, $constraint->onDelete)))->execute();
  }

  public function dropForeignKey($name)
  {
    s::db()->query(sprintf("ALTER TABLE `{$this->name}` DROP FOREIGN KEY %s`", $name))->execute();
  }

  public function loadSchema()
  {
    if ($this->exists()) {
      $_columns = s::db()->query("SHOW COLUMNS FROM {$this->name}")->execute()->fetchAll();

      $this->columns = array();
      $this->indexes = array();
      $this->constraints = array();

      foreach ($_columns as &$_column) {
        $column = new Simplify_Db_Column($_column['Field']);

        if (preg_match('/([a-z]+)(?:\((.+)\))?( unsigned)*( zerofill)*/', $_column['Type'], $type)) {
          $column->type = $type[1];
          $column->size = $type[2];
          $column->unsigned = !empty($type[3]);
          $column->zerofill = !empty($type[4]);
        }

        if (preg_match('/auto_increment/', $_column['Extra'])) {
          $column->autoIncrement = true;
        }

        $column->field = $_column['Field'];
        $column->null = $_column['Null'] == 'YES';
        $column->default = $_column['Default'];

        $this->columns[$_column['Field']] = $column;
        $this->_schema['columns'][$_column['Field']] = $column;
      }

      $_indexes = s::db()->query("SHOW INDEX FROM {$this->name}")->execute()->fetchAll();

      foreach ($_indexes as $_index) {
        if (empty($this->indexes[$_index['Key_name']])) {
          $index = new Simplify_Db_Index();
          $index->unique = empty($_index['Non_unique']);

          $this->indexes[$_index['Key_name']] = $index;
        }

        $this->indexes[$_index['Key_name']]->fields[$_index['Seq_in_index']] = $_index['Column_name'];

        $this->_schema['indexes'][$_index['Key_name']] = $this->indexes[$_index['Key_name']];
      }

      $db = sy_get_param(s::db()->getParams(), 'name');

      $sql = "
        SELECT CONSTRAINT_NAME, TABLE_NAME, COLUMN_NAME,
          REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
        FROM information_schema.KEY_COLUMN_USAGE
				WHERE TABLE_SCHEMA = '{$db}' AND REFERENCED_TABLE_NAME IS NOT NULL
          AND (REFERENCED_TABLE_NAME = '{$this->name}' OR TABLE_NAME = '{$this->name}')
      ";

      $_constraints = s::db()->query($sql)->execute()->fetchAll();

      foreach ($_constraints as $_constraint) {
        $constraint = new Simplify_Db_Constraint();
        $constraint->table = $_constraint['TABLE_NAME'];
        $constraint->column = $_constraint['COLUMN_NAME'];
        $constraint->referencedTable = $_constraint['REFERENCED_TABLE_NAME'];
        $constraint->referencedColumn = $_constraint['REFERENCED_COLUMN_NAME'];

        $this->constraints[$_constraint['CONSTRAINT_NAME']] = $constraint;
        $this->_schema['constraints'][$_constraint['CONSTRAINT_NAME']] = $constraint;
      }
    }
  }

  /**
   * Check if table exists
   *
   * @return boolean
   */
  public function exists()
  {
    $tables = false;

    if (!empty($this->name)) {
      $tables = s::db()->query("SHOW TABLES")->executeRaw()->fetchCol();
    }

    return $tables && in_array($this->name, $tables);
  }

  public function create()
  {
    if (!$this->exists()) {
      if (empty($this->columns)) {
        throw new Simplify_Db_Exception('Cannot create empty table');
      }

      $create = "CREATE TABLE `%1\$s` (\n%2\$s\n) \nCOLLATE='utf8_general_ci' ENGINE=InnoDB";

      $s = array();

      foreach ($this->columns as $field => $column) {
        $s[] = '  ' . $column->getColumnSql();
      }

      foreach ($this->indexes as $type => $index) {
        if ($type == 'PRIMARY') {
          $s[] = sprintf('  PRIMARY KEY (`%s`)', implode('` , `', (array) $index->fields));
        }
        elseif (!empty($index->unique)) {
          $s[] = sprintf('  UNIQUE INDEX `%s` (`%s`)', $type, implode('` , `', (array) $index->fields));
        }
        else {
          $s[] = sprintf('  INDEX `%s` (`%s`)', $type, implode('` , `', (array) $index->fields));
        }
      }

      $sql = sprintf($create, $this->name, implode(",\n", $s));

      s::db()->query($sql)->execute();

      $this->loadSchema();
    }
  }

  public function save()
  {
    if (!$this->exists()) {
      return $this->create();
    }

    $add = array_diff_key($this->columns, $this->_schema['columns']);
    $rem = array_diff_key($this->_schema['columns'], $this->columns);

    $upd = array();

    $int = array_intersect_key($this->columns, $this->_schema['columns']);

    foreach ($int as $field => $column) {
      $old = $this->_schema['columns'][$field]->getColumnSql();
      $new = $this->columns[$field]->getColumnSql();

      if ($old !== $new) {
        $upd[$field] = $column;
      }
    }

    array_walk($add, array($this, 'addColumn'));
    array_walk($rem, array($this, 'dropColumn'));
    array_walk($upd, array($this, 'changeColumn'));

    $this->loadSchema();
  }

}
