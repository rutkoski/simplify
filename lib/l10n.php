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
 * @copyright Copyright 2008 Rodrigo Rutkoski Rodrigues
 */

/**
 *
 * Localization shortcut functions
 *
 */

if (!function_exists('__')) {

  /**
   * Get the localized version of $msgid
   *
   * @param string $msgid
   * @return string
   */
  function __($msgid)
  {
    return s::l10n()->gettext($msgid);
  }
}

if (!function_exists('_n')) {

  /**
   * Get the localized version of $single or $plural, depending on $number
   *
   * @param string $single
   * @param string $plural
   * @param int $number
   * @return string
   */
  function _n($single, $plural, $number)
  {
    return s::l10n()->ngettext($single, $plural, $number);
  }
}

if (!function_exists('_d')) {

  /**
   * Get the localized version of $msgid in a specific $domain
   *
   * @param string $domain
   * @param string $msgid
   * @return string
   */
  function _d($domain, $msgid)
  {
    return s::l10n()->dgettext($domain, $msgid);
  }
}

if (!function_exists('_dn')) {

  /**
   * Get the localized version of $single or $plural, depending on $number, in a specific $domain
   *
   * @param string $domain
   * @param string $single
   * @param string $plural
   * @param int $number
   * @return string
   */
  function _dn($domain, $single, $plural, $number)
  {
    return s::l10n()->dngettext($domain, $single, $plural, $number);
  }
}
