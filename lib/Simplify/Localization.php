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

namespace Simplify;

/**
 *
 * Base class for localization
 *
 */
abstract class Localization
{

  /**
   * Default text domain
   *
   * @var string
   */
  const DOMAIN_DEFAULT = 'default';

  /**
   * Current text domain
   *
   * @var string
   */
  protected $domain = Localization::DOMAIN_DEFAULT;

  /**
   * Current locale
   *
   * @var string
   */
  protected $locale = 'pt-BR';

  /**
   * Get/set current locale
   *
   * @param string $locale
   */
  public function locale($locale = null)
  {
    if (!empty($locale)) {
      $this->locale = $locale;
    }

    return $this->locale;
  }

  /**
   * Get/set current text domain
   *
   * @param string $domain
   * @return string
   */
  public function domain($domain = null)
  {
    if (!empty($domain)) {
      $this->domain = $domain;
    }

    return $this->domain;
  }

  /**
   * Get the localized version of $msgid
   *
   * @param string $msgid
   * @return string
   */
  public function gettext($msgid)
  {
    return $this->dgettext($this->domain(), $msgid);
  }

  /**
   * Get the localized version of $single or $plural, depending on $number
   *
   * @param string $single
   * @param string $plural
   * @param int $number
   * @return string
   */
  public function ngettext($single, $plural, $number)
  {
    return $this->dngettext($this->domain(), $single, $plural, $number);
  }

  /**
   * Get the localized version of $msgid in a specific $domain
   *
   * @param string $domain
   * @param string $msgid
   * @return string
   */
  public function dgettext($domain, $msgid)
  {
  }

  /**
   * Get the localized version of $single or $plural, depending on $number, in a specific $domain
   *
   * @param string $domain
   * @param string $single
   * @param string $plural
   * @param int $number
   * @return string
   */
  public function dngettext($domain, $single, $plural, $number)
  {
  }

  /**
   * Add a localization library to $domain
   *
   * @param string $name
   * @param string $domain
   */
  public function add($name, $domain = Localization::DOMAIN_DEFAULT)
  {
  }

}
