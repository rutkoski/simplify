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

namespace Simplify;

/**
 *
 * Dictionary view of session data
 *
 */
class Session implements DictionaryInterface
{

  const MESSAGES_NOTICES = 'notices';

  const MESSAGES_WARNINGS = 'warnings';

  /**
   * Singleton instance of Session
   *
   * @var Session
   */
  private static $instance;

  /**
   * Get the singletoin instance of Session
   *
   * @return Session
   */
  public static function getInstance()
  {
    if (empty(self::$instance))
      self::$instance = new self();
    return self::$instance;
  }

  /**
   * Get the session id
   *
   * @return string
   */
  public static function id()
  {
    return session_id();
  }

  /**
   * Constructor
   *
   * @return void
   */
  private function __construct()
  {
    $this->start();
  }

  /**
   * Start a session
   *
   * @param string $id session id
   * @return Session
   */
  public function start($id = null)
  {
    if (!empty($id)) {
      session_id($id);
    }

    if(session_id() == '') {
      session_start();
    }

    return $this;
  }

  /**
   * Destroy current session
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
   * Get/set a temporary session variable.
   * Call Session::flash($name, $value) to set it's value.
   * Call Session::flash($name) to get it's value and delete the variable.
   *
   * @param string $name
   * @param mixed $value Optional
   * @return mixed
   */
  public function flash($name, $value = null)
  {
    if (func_num_args() === 1) {
      return isset($_SESSION['__flash__']) ? sy_get_param($_SESSION['__flash__'], $name) : null;
    }
    else {
      $_SESSION['__flash__'][$name] = $value;
    }
  }

  /**
   *
   * @param string|string[] $messages the messages
   * @param string $namespace group messages in a namespace
   * @return string[]
   */
  public function warnings($messages = null, $namespace = '*')
  {
    return $this->flashMessages(self::MESSAGES_WARNINGS, $messages, $namespace);
  }

  /**
   *
   * @param string|string[] $messages the messages
   * @param string $namespace group messages in a namespace
   * @return string[]
   */
  public function flushWarnings($messages = null, $namespace = '*')
  {
    $messages = $this->flashMessages(self::MESSAGES_WARNINGS, $messages, $namespace);
    $this->clearMessages(self::MESSAGES_WARNINGS);
    return $messages;
  }

  /**
   *
   * @param string|string[] $messages the messages
   * @param string $namespace group messages in a namespace
   * @return string[]
   */
  public function notices($messages = null, $namespace = '*')
  {
    return $this->flashMessages(self::MESSAGES_NOTICES, $messages, $namespace);
  }

  /**
   *
   * @param string|string[] $messages the messages
   * @param string $namespace group messages in a namespace
   * @return string[]
   */
  public function flushNotices($messages = null, $namespace = '*')
  {
    $messages = $this->flashMessages(self::MESSAGES_NOTICES, $messages, $namespace);
    $this->clearMessages(self::MESSAGES_NOTICES);
    return $messages;
  }

  /**
   *
   */
  public function clearMessages($type = null)
  {
    if ($type && isset($_SESSION['__messages__'][$type])) {
      unset($_SESSION['__messages__'][$type]);
    }
    elseif (isset($_SESSION['__messages__'])) {
      unset($_SESSION['__messages__']);
    }
  }

  /**
   *
   * @param string $type message type
   * @param string|string[] $messages the messages
   * @param string $namespace group messages in a namespace
   * @return string[]
   */
  public function flashMessages($type, $messages = null, $namespace = '*')
  {
    if (!isset($_SESSION['__messages__'][$type][$namespace])) {
      $_SESSION['__messages__'][$type][$namespace] = array();
    }

    if (func_num_args() > 1) {
      $_SESSION['__messages__'][$type][$namespace] = array_unique(
        array_filter(array_merge((array) $_SESSION['__messages__'][$type][$namespace], (array) $messages)));
    }

    return $_SESSION['__messages__'][$type][$namespace];
  }

  /**
   * (non-PHPdoc)
   * @see DictionaryInterface::copyAll()
   */
  public function copyAll($data, $flags = 0)
  {
    if (empty($data))
      return;

    if ($data instanceof DictionaryInterface) {
      $data = $data->getAll();
    }

    foreach ($data as $name => $value) {
      if ((Dictionary::FILTER_NULL & $flags) == $flags && is_null($value))
        continue;

      if ((Dictionary::FILTER_EMPTY & $flags) == $flags && empty($value))
        continue;

      $this->set($name, $value);
    }

    return $this;
  }

  /**
   * (non-PHPdoc)
   * @see DictionaryInterface::del()
   */
  public function del($name)
  {
    if (isset($_SESSION[$name])) {
      unset($_SESSION[$name]);
    }

    return $this;
  }

  /**
   * (non-PHPdoc)
   * @see DictionaryInterface::get()
   */
  public function get($name, $default = null, $flags = 0)
  {
    if ($this->has($name, $flags)) {
      return $_SESSION[$name];
    }

    return $default;
  }

  /**
   * (non-PHPdoc)
   * @see DictionaryInterface::getAll()
   */
  public function getAll($flags = 0)
  {
    return $_SESSION;
  }

  /**
   * (non-PHPdoc)
   * @see DictionaryInterface::getNames()
   */
  public function getNames()
  {
    return array_keys($_SESSION);
  }

  /**
   * (non-PHPdoc)
   * @see DictionaryInterface::has()
   */
  public function has($name, $flags = 0)
  {
    if (!isset($_SESSION[$name])) {
      return false;
    }

    if ((Dictionary::FILTER_NULL & $flags) == $flags && is_null($_SESSION[$name])) {
      return false;
    }

    if ((Dictionary::FILTER_EMPTY & $flags) == $flags && empty($_SESSION[$name])) {
      return false;
    }

    return true;
  }

  /**
   * (non-PHPdoc)
   * @see DictionaryInterface::reset()
   */
  public function reset($data = null)
  {
    $_SESSION = array();
    return $this->copyAll($data);
  }

  /**
   * (non-PHPdoc)
   * @see DictionaryInterface::set()
   */
  public function set($name, $value)
  {
    $_SESSION[$name] = $value;
    return $this;
  }

  /**
   * (non-PHPdoc)
   * @see JsonSerializable::jsonSerialize()
   */
  public function jsonSerialize()
  {
    return $_SESSION;
  }

}
