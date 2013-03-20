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

if (! function_exists('syck_load')) {
  require_once (SY_DIR . '/lib/spyc.php');
}

/**
 * YAML wrapper.
 *
 * @author Rodrigo Rutkoski Rodrigues <rutkoski@gmail.com>
 */
class Data_Syml
{

  /**
   * Read file and convert it's contents from YAML.
   *
   * @param unknown_type $filename
   */
  public static function read($filename)
  {
    return self::load(file_get_contents($filename));
  }

  /**
   * Convert to YAML and write it to a file.
   *
   * @param unknown_type $file
   * @param unknown_type $data
   */
  public static function write($file, $data)
  {
    file_put_contents($file, self::dump($data));
  }

  /**
   * Convert from YAML.
   *
   * @param string $yaml
   */
  public static function load($yaml)
  {
    if (function_exists('syck_load')) {
      return syck_load($yaml);
    }

    return spyc_load($yaml);
  }

  /**
   * Converto to YAML.
   *
   * @param mixed $data
   */
  public static function dump($data)
  {
    if (function_exists('syck_dump')) {
      return syck_dump($data);
    }

    return Spyc::YAMLDump($data);
  }

}
