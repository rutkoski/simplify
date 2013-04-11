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

  /**
   * (non-PHPdoc)
   * @see Simplify_Localization::add()
   */
  public function add($name, $domain = Simplify_Localization::DOMAIN_DEFAULT)
  {
    $path = s::config()->get('locale_dir', APP_DIR . '/language');
    $path .= '/' . $this->locale . '/' . $domain . '.php';

    $this->lang[$domain] += require_once($path);
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
    if (isset($this->lang[$domain][$msgid])) {
      return $number == 1 ? $this->lang[$domain][$msgid][0] : $this->lang[$domain][$msgid][1];
    }

    return $number == 1 ? $single : $plural;
  }

}
