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
 * A Simplify_Dictionary holds name/value pairs. All names are unique.
 *
 * @author Rodrigo Rutkoski Rodrigues <rutkoski@gmail.com>
 */
interface Simplify_DictionaryInterface
{

  /**
   * Copy all names and values from $data
   *
   * @param mixed $data
   * @return Simplify_DictionaryInterface this method sould return $this
   */
  public function copyAll($data, $flags = 0);

  /**
   * Delete a name/value pair from the Simplify_Dictionary
   *
   * @param string $name
   * @return Simplify_DictionaryInterface this method sould return $this
   */
  public function del($name);

  /**
   * Get the value for a given $name
   *
   * Accepts an optional second parameter $default, a value that will be
   * returned in case name is not found in the Simplify_Dictionary or if it doesn't match
   * the optional third parameter $filter.
   *
   * Third parameter, $filter, is optional and follows the same principle of
   * Simplify_DictionaryInterface::has($name). If Simplify_DictionaryInterface::has($name, $filter) returns false,
   * the method returns $default.
   *
   * @param string $name
   * @param mixed $default optional
   * @param int $flags optional
   * @return mixed
   */
  public function get($name, $default = null, $flags = 0);

  /**
   * Get an associative with all name/value pairs from the Simplify_Dictionary
   *
   * @return array
   */
  public function getAll($flags = 0);

  /**
   * Get all names from the Simplify_Dictionary
   *
   * @return array
   */
  public function getNames();

  /**
   * Check if $name exists in the Simplify_Dictionary
   *
   * Accepts a second optional argument $filter that
   *
   * Second parameter, $filter, is optional and accepts on of these values:
   * - Simplify_Dictionary::FILTER_NULL: if the value for $name is null, the method
   * returns false
   * - Simplify_Dictionary::FILTER_EMPTY: if the value for $name is empty, the method
   * returns false
   *
   * By default, if $filer is omitted, the method returns true if $name is not
   * set in the Simplify_Dictionary.
   *
   * @param string $name
   * @return boolean
   */
  public function has($name, $flags = 0);

  /**
   * Resets (deletes all name/value pairs) the Simplify_Dictionary.
   *
   * Accepts a second optional argument $data. If $data is informed,
   * Simplify_DictionaryInterface::copyAll($data) is called after reset.
   *
   * @param mixed $data optional
   * @return Simplify_DictionaryInterface this method sould return $this
   */
  public function reset($data = null);

  /**
   * Set the $value for $name.
   *
   * @param string $name
   * @param mixed $value
   * @return Simplify_DictionaryInterface this method sould return $this
   */
  public function set($name, $value);

}
