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

/**
 *
 * Base class and factory for DBAL implementations
 *
 */
abstract class Simplify_Db_Database implements Simplify_Db_DatabaseInterface
{

  /**
   * Default DBAL engine.
   *
   * @var string
   */
  public static $defaultEngine = 'Simplify_Db_Pdo_Database';

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
   * @see Simplify_Db_DatabaseInterface::query()
   */
  public function query($sql = null)
  {
    if ($sql instanceof Simplify_Db_QueryObjectInterface) {
      return $sql;
    }

    return $this->factoryQueryObject()->sql($sql);
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Db_DatabaseInterface::insert()
   */
  public function insert($table = null, $data = null)
  {
    return $this->factoryQueryObject()->insert($table, $data);
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Db_DatabaseInterface::update()
   */
  public function update($table = null, $data = null, $where = null)
  {
    return $this->factoryQueryObject()->update($table, $data, $where);
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Db_DatabaseInterface::delete()
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
   * @return Simplify_Db_DatabaseInterface
   */
  public static function getInstance($id = 'default', $engine = null, $params = null)
  {
    $config = s::config();

    if (! isset(Simplify_Db_Database::$instances[$id])) {
      if (empty($engine)) {
        $engine = Simplify_Db_Database::$defaultEngine;
      }

      $class = $engine;

      if (! isset($config['database'][$id])) {
        throw new Exception("Database configuration for <b>$id</b> not found");
      }

      $_params = $config['database'][$id]['*'];

      if (isset($config['database'][$id][$_SERVER['SERVER_NAME']])) {
        $_params = array_merge($_params, $config['database'][$id][$_SERVER['SERVER_NAME']]);
      }

      if (is_array($params) && ! empty($params)) {
        $_params = array_merge($_params, $params);
      }

      $dao = new $class($_params);

      Simplify_Db_Database::$instances[$id] = $dao;
    }

    return Simplify_Db_Database::$instances[$id];
  }

  /**
   * Get/set log information
   *
   * @param array|null $data log data
   * @return array
   */
  public static function log($data = null)
  {
    if (! empty($data)) {
      self::$log[] = array_filter($data);
    }

    return self::$log;
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Db_DatabaseInterface::quote()
   */
  public function quote($value, $type = null)
  {
    if (! is_numeric($value)) {
      return "'{$value}'";
    }
    return $value;
  }

}
