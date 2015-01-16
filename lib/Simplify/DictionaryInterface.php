<?php

namespace Simplify;

use JsonSerializable;

interface DictionaryInterface extends JsonSerializable
{

  /**
   * Copy all names and values from $data
   *
   * @param mixed $data
   * @return DictionaryInterface this method sould return $this
   */
  public function copyAll($data, $flags = 0);

  /**
   * Delete a name/value pair from the Dictionary
   *
   * @param string $name
   * @return DictionaryInterface this method sould return $this
   */
  public function del($name);

  /**
   * Get the value for a given $name
   *
   * Accepts an optional second parameter $default, a value that will be
   * returned in case name is not found in the Dictionary or if it doesn't match
   * the optional third parameter $filter.
   *
   * Third parameter, $filter, is optional and follows the same principle of
   * DictionaryInterface::has($name). If DictionaryInterface::has($name, $filter) returns false,
   * the method returns $default.
   *
   * @param string $name
   * @param mixed $default optional
   * @param int $flags optional
   * @return mixed
   */
  public function get($name, $default = null, $flags = 0);

  /**
   * Get an associative with all name/value pairs from the Dictionary
   *
   * @return array
   */
  public function getAll($flags = 0);

  /**
   * Get all names from the Dictionary
   *
   * @return array
   */
  public function getNames();

  /**
   * Check if $name exists in the Dictionary
   *
   * Accepts a second optional argument $filter that
   *
   * Second parameter, $filter, is optional and accepts on of these values:
   * - Dictionary::FILTER_NULL: if the value for $name is null, the method
   * returns false
   * - Dictionary::FILTER_EMPTY: if the value for $name is empty, the method
   * returns false
   *
   * By default, if $filer is omitted, the method returns true if $name is not
   * set in the Dictionary.
   *
   * @param string $name
   * @return boolean
   */
  public function has($name, $flags = 0);

  /**
   * Resets (deletes all name/value pairs) the Dictionary.
   *
   * Accepts a second optional argument $data. If $data is informed,
   * DictionaryInterface::copyAll($data) is called after reset.
   *
   * @param mixed $data optional
   * @return DictionaryInterface this method sould return $this
   */
  public function reset($data = null);

  /**
   * Set the $value for $name.
   *
   * @param string $name
   * @param mixed $value
   * @return DictionaryInterface this method sould return $this
   */
  public function set($name, $value);

}
