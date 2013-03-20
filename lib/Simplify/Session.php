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
 * @author Rodrigo Rutkoski Rodrigues, <rutkoski@gmail.com>
 */

/**
 * Session data wrapper.
 *
 * @author Rodrigo Rutkoski Rodrigues <rutkoski@gmail.com>
 */
class Simplify_Session implements Simplify_DictionaryInterface
{

  /**
   * Singleton instance of Session.
   *
   * @var Session
   */
  private static $instance;

  /**
   * Get the instance of Session.
   *
   * @return Session
   */
  public static function getInstance()
  {
    if (empty(self::$instance))
      self::$instance = new self();
    return self::$instance;
  }

  public static function id()
  {
    return session_id();
  }

  /**
   * Constructor.
   *
   * @return void
   */
  private function __construct()
  {
    $this->start();
  }

  /**
   * Start a session.
   *
   * @param string $id Session id.
   * @return Session
   */
  public function start($id = null)
  {
    if (!empty($id)) {
      session_id($id);
    }

    session_start();

    return $this;
  }

  /**
   * Destroy the current session.
   *
   * @return Session
   */
  public function destroy()
  {
    $this->reset();

    if (isset($_COOKIE[session_name()])) {
      setcookie(session_name(), '', time() - 42000, '/');
    }

    session_destroy();

    return $this;
  }

  /**
   * Set a session variable that will be deleted the next time it is caught.
   *
   * @return mixed
   */
  public function flash($name, $value = null)
  {
    $data = $this->get('__flash__', array());

    if (func_num_args() === 1) {
      $value = sy_get_param($data, $name);
      unset($data[$name]);
    }
    else {
      $data[$name] = $value;
    }

    $this->set('__flash__', $data);

    return $value;
  }

  /**
   *
   */
  public function copyAll($data, $flags = 0)
  {
    if (empty($data))
      return;

    if ($data instanceof Simplify_DictionaryInterface) {
      $data = $data->getAll();
    }

    foreach ($data as $name => $value) {
      if ((Simplify_Dictionary::FILTER_NULL & $flags) == $flags && is_null($value))
        continue;

      if ((Simplify_Dictionary::FILTER_EMPTY & $flags) == $flags && empty($value))
        continue;

      $this->set($name, $value);
    }

    return $this;
  }

  /**
   *
   */
  public function del($name)
  {
    if (isset($_SESSION[$name])) {
      unset($_SESSION[$name]);
    }

    return $this;
  }

  /**
   *
   */
  public function get($name, $default = null, $flags = 0)
  {
    if ($this->has($name, $flags)) {
      return $_SESSION[$name];
    }

    return $default;
  }

  /**
   *
   */
  public function getAll($flags = 0)
  {
    return $_SESSION;
  }

  /**
   *
   */
  public function getNames()
  {
    return array_keys($_SESSION);
  }

  /**
   *
   */
  public function has($name, $flags = 0)
  {
    if (!isset($_SESSION[$name])) {
      return false;
    }

    if ((Simplify_Dictionary::FILTER_NULL & $flags) == $flags && is_null($_SESSION[$name])) {
      return false;
    }

    if ((Simplify_Dictionary::FILTER_EMPTY & $flags) == $flags && empty($_SESSION[$name])) {
      return false;
    }

    return true;
  }

  /**
   *
   */
  public function reset($data = null)
  {
    $_SESSION = array();
    return $this->copyAll($data);
  }

  /**
   *
   */
  public function set($name, $value)
  {
    $_SESSION[$name] = $value;
    return $this;
  }

}
