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

namespace Simplify\Db;

use Simplify;

/**
 *
 * Base class and factory for DBAL implementations
 *
 */
abstract class Database implements DatabaseInterface
{

  /**
   * Default DBAL engine.
   *
   * @var string
   */
  public static $defaultEngine = 'Simplify\Db\Pdo\Database';

  /**
   *
   * @var array
   */
  private static $instances = array();

  /**
   * Parameters
   *
   * @var array
   */
  protected $params;

  /**
   *
   * @var mixed[]
   */
  protected static $log = array();

  /**
   * Constructor
   *
   * @param array $params parameters
   * @return void
   */
  protected function __construct($params = null)
  {
    $this->params = $params;
  }

  /**
   * (non-PHPdoc)
   * @see Simplify\Db\DatabaseInterface::query()
   */
  public function query($sql = null)
  {
    if ($sql instanceof QueryObjectInterface) {
      return $sql;
    }

    return $this->factoryQueryObject()->sql($sql);
  }

  /**
   * (non-PHPdoc)
   * @see Simplify\Db\DatabaseInterface::insert()
   */
  public function insert($table = null, $data = null)
  {
    return $this->factoryQueryObject()->insert($table, $data);
  }

  /**
   * (non-PHPdoc)
   * @see \Simplify\Db\DatabaseInterface::update()
   */
  public function update($table = null, $data = null, $where = null)
  {
    return $this->factoryQueryObject()->update($table, $data, $where);
  }

  /**
   * (non-PHPdoc)
   * @see Simplify\Db\DatabaseInterface::delete()
   */
  public function delete($table = null, $where = null)
  {
    return $this->factoryQueryObject()->delete($table, $where);
  }

  /**
   * Factory and return the DBAL instance for a given configuration and of a certain engine.
   *
   * @param string $id configuration profile
   * @param string $engine DBAL engine
   * @param mixed $params extra parameters
   * @return Simplify\Db\DatabaseInterface
   */
  public static function getInstance($id = 'default', $engine = null, $params = null)
  {
    if (!isset(Database::$instances[$id])) {
      if (empty($engine)) {
        $engine = Database::$defaultEngine;
      }

      $class = $engine;

      $params = self::getParams($id, $params);

      $dao = new $class($params);

      Database::$instances[$id] = $dao;
    }

    return Database::$instances[$id];
  }

  /**
   *
   *
   * @param string $id configuration profile
   * @param mixed $params extra parameters
   * @return array database connection parameters
   */
  public static function getParams($id = 'default', $params = null)
  {
    $config = Simplify::config();

    $_params = $config['database'][$id]['*'];

    foreach ($config['database'][$id] as $__params) {
      if (isset($__params['matchHost'])) {
        $regex = $__params['matchHost'];

        if (!preg_match('/^\d{1-3}\.\d{1-3}\.\d{1-3}\.\d{1-3}$/', $regex)) {
          $regex = '([^.]+\.)?' . preg_quote($regex);
        }
        else {
          $regex = preg_quote($regex);
        }

        if (isset($_SERVER['SERVER_NAME'])) {
          $regex = '/' . $regex . '$/i';

          if (preg_match($regex, $_SERVER['SERVER_NAME'])) {
            $_params = array_merge($_params, $__params);
          }
        }
      }
    }

    if (is_array($params) && !empty($params)) {
      $_params = array_merge($_params, $params);
    }

    return $_params;
  }

  /**
   * Get/set log information
   *
   * @param array|null $data log data
   * @return array
   */
  public static function log($data = null)
  {
    if (!empty($data)) {
      self::$log[] = array_filter($data);
    }

    return self::$log;
  }

  /**
   * (non-PHPdoc)
   * @see Simplify\Db\DatabaseInterface::quote()
   */
  public function quote($value, $type = null)
  {
    if (!is_numeric($value)) {
      return "'{$value}'";
    }
    return $value;
  }

}
