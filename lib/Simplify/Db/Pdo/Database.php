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

namespace Simplify\Db\Pdo;

use Simplify\Db\DatabaseException;

/**
 *
 * PDO Database
 *
 */
class Database extends \Simplify\Db\Database
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
   * Constructor
   *
   * @param array $params
   * @return void
   */
  protected function __construct($params)
  {
    parent::__construct($params);

    $this->dsn = array('driver' => sy_get_param($this->params, 'type', 'mysql'),
      'username' => sy_get_param($this->params, 'username'), 'password' => sy_get_param($this->params, 'password'),
      'host' => sy_get_param($this->params, 'host', 'localhost'), 'database' => sy_get_param($this->params, 'name'),
      'charset' => sy_get_param($this->params, 'charset', 'utf8'));

    $this->options = array(\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8', \PDO::ATTR_PERSISTENT => true);
  }

  /**
   * (non-PHPdoc)
   * @see Simplify\Db\DatabaseInterface::lastInsertId()
   */
  public function lastInsertId()
  {
    return $this->db()->lastInsertID();
  }

  /**
   * (non-PHPdoc)
   * @see Simplify\Db\DatabaseInterface::beginTransaction()
   */
  public function beginTransaction()
  {
    $this->db()->beginTransaction();
  }

  /**
   * (non-PHPdoc)
   * @see Simplify\Db\DatabaseInterface::commit()
   */
  public function commit()
  {
    $this->db()->commit();
  }

  /**
   * (non-PHPdoc)
   * @see Simplify\Db\DatabaseInterface::rollback()
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
   * @see Simplify\Db\DatabaseInterface::connect()
   */
  public function connect()
  {
    if (empty($this->db)) {
      $dsn = $this->dsn['driver'] . ':host=' . $this->dsn['host'] . ';dbname=' . $this->dsn['database'] . ';charset=' .
         $this->dsn['charset'];

      try {
        $this->db = new \PDO($dsn, sy_get_param($this->dsn, 'username'), sy_get_param($this->dsn, 'password'),
          $this->options);
      }
      catch (\PDOException $e) {
        throw new DatabaseException('Database connection failed with message: ' . $e->getMessage());
      }
    }

    return $this;
  }

  /**
   * (non-PHPdoc)
   * @see Simplify\Db\DatabaseInterface::disconnect()
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
   * @see Simplify\Db\DatabaseInterface::factoryQueryObject()
   */
  public function factoryQueryObject()
  {
    return new QueryObject($this);
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
    if ($res !== false) {
      $error = $res->errorInfo();
    }

    if (!empty($error[2])) {
      $args = func_get_args();
      unset($args[0]);

      $info = array();
      $info[] = "{$error[2]}({$error[1]})";
      foreach ($args as $arg) {
        $info[] = var_export($arg, true);
      }

      $msg = implode("\n", array_filter($info));

      throw DatabaseException::factoryException($error[0], $msg, $error[1]);
    }
  }

}
