<?php

class Simplify_Db_Pdo_Database extends Simplify_Db_Database
{

  /**
   *
   * @var PDO
   */
  private $db;

  /**
   *
   * @var array
   */
  protected $dsn;

  /**
   *
   * @var array
   */
  protected $options;

  /**
   * Constructor.
   *
   * @param array $params
   * @return void
   */
  protected function __construct($params)
  {
    parent::__construct($params);

    $this->dsn = array(
      'driver' => sy_get_param($this->params, 'type', 'mysql'), 'username' => sy_get_param($this->params, 'username'),
      'password' => sy_get_param($this->params, 'password'), 'host' => sy_get_param($this->params, 'host', 'localhost'),
      'database' => sy_get_param($this->params, 'name'), 'charset' => sy_get_param($this->params, 'charset', 'utf8')
    );

    $this->options = array(
      PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
      PDO::ATTR_PERSISTENT => true
    );
  }

  /**
   * (non-PHPdoc)
   * @see simplify/kernel/dao/api/IDataAccessObject#lastInsertId()
   */
  public function lastInsertId()
  {
    return $this->db()->lastInsertID();
  }

  /**
   * (non-PHPdoc)
   * @see simplify/kernel/dao/DAO#beginTransaction()
   */
  public function beginTransaction()
  {
    $this->db()->beginTransaction();
  }

  /**
   * (non-PHPdoc)
   * @see simplify/kernel/dao/DAO#commit()
   */
  public function commit()
  {
    $this->db()->commit();
  }

  /**
   * (non-PHPdoc)
   * @see simplify/kernel/dao/DAO#rollback()
   */
  public function rollback()
  {
    $this->db()->rollBack();
  }

  /**
   *
   * @return MDB2_Driver_Common
   */
  public function db()
  {
    return $this->connect()->db;
  }

  /**
   * (non-PHPdoc)
   * @see simplify/kernel/dao/DAO#connect()
   */
  public function connect()
  {
    if (empty($this->db)) {
      $dsn = $this->dsn['driver'] . ':host=' . $this->dsn['host'] . ';dbname=' . $this->dsn['database'] . ';charset=' . $this->dsn['charset'];

      try {
        $this->db = new PDO($dsn, sy_get_param($this->dsn, 'username'), sy_get_param($this->dsn, 'password'), $this->options);
      }
      catch (PDOException $e) {
        throw new Simplify_Db_DatabaseException('Database connection failed with message: ' . $e->getMessage());
      }
    }

    return $this;
  }

  /**
   * (non-PHPdoc)
   * @see simplify/kernel/dao/DAO#disconnect()
   */
  public function disconnect()
  {
    if ($this->db) {
      $this->db = null;
    }

    return $this;
  }

  /**
   * (non-PHPdoc)
   * @see simplify/kernel/dao/DAO#quote($value, $type)
   */
  /*public function quote($value, $type = null)
  {
    return $this->db()->quote($value, $type);
  }*/

  /**
   * (non-PHPdoc)
   * @see simplify/kernel/dao/DAO#factoryQueryObject()
   */
  public function factoryQueryObject()
  {
    return new Simplify_Db_Pdo_QueryObject($this);
  }

  /**
   * Check if $res is MDB2_Error
   *
   * @param mixed $res
   * @throws Exception if any errors ocurred
   * @return void
   */
  public static function validate($res)
  {
    $error = $res->errorInfo();

    if (! empty($error[2])) {
      $args = func_get_args();
      unset($args[0]);

      $info = array();
      $info[] = "{$error[2]}({$error[1]})";
      foreach ($args as $arg) {
        $info[] = var_export($arg, true);
      }

      $msg = implode("\n", array_filter($info));

      throw Simplify_Db_DatabaseException::factoryException($error[0], $msg, $error[1]);
    }
  }

}
