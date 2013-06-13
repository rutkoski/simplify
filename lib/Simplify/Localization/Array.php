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
 * Localization that uses an array of strings identified by a message id
 *
 */
class Simplify_Localization_Array extends Simplify_Localization
{

  /**
   *
   * @var string[string]
   */
  protected $lang = array();

  public function __construct()
  {
    $this->add('default', Simplify_Localization::DOMAIN_DEFAULT);
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Localization::add()
   */
  public function add($name, $domain = Simplify_Localization::DOMAIN_DEFAULT)
  {
    $filename = s::config()->get('locale_dir', APP_DIR . '/language', Simplify_Dictionary::FILTER_EMPTY);
    $filename .= '/' . $this->locale . '/' . $domain . '.php';

    if (! file_exists($filename)) {
      throw new Exception("Localization file <b>{$name}</b> for domain <b>{$domain}</b> not found: <b>{$filename}</b>");
    }

    $lang = require_once ($filename);

    $this->lang[$domain] = array_merge((array) $this->lang[$domain], (array) $lang);
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Localization::dgettext()
   */
  public function dgettext($domain, $msgid)
  {
    if (isset($this->lang[$domain][$msgid])) {
      return $this->lang[$domain][$msgid];
    }

    return $msgid;
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Localization::dngettext()
   */
  public function dngettext($domain, $single, $plural, $number)
  {
    if ($number == 1) {
      $value = isset($this->lang[$domain][$single]) ? $this->lang[$domain][$single] : $single;
    } else {
      $value = isset($this->lang[$domain][$plural]) ? $this->lang[$domain][$plural] : $plural;
    }

    return $value;
  }

}
