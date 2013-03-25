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
 * Html helper
 *
 */
class Simplify_View_Helper_Html extends Simplify_View_Helper
{

  /**
   *
   * @return Simplify_HtmlElement
   */
  public function image($src, $alt = '', $attrs = array(), $params = array())
  {
    if (! sy_url_is_absolute($src)) {
      $src = sy_fix_url(s::config()->get('theme_url') . '/images/' . $src);
    }

    $img = e('<img>', $attrs)->attr('src', $src)->attr('alt', $alt);

    return $this->output($img);
  }

  /**
   *
   * @return Simplify_HtmlElement
   */
  public function css($href, $attrs = array(), $params = array())
  {
    $href = sy_absolute_url($href, '/css');

    $href = sy_fix_extension($href, 'css');

    $link = e('<link>', $attrs)->attr('href', $href)->attr('type', 'text/css')->attr('rel', 'stylesheet');

    return $this->output($link);
  }

  /**
   *
   * @return Simplify_HtmlElement
   */
  public function js($href, $attrs = array(), $params = array())
  {
    $href = sy_absolute_url($href, '/javascript');

    $href = sy_fix_extension($href, 'js');

    $link = e('<script>', $attrs)->attr('src', $href)->attr('type', 'text/javascript');

    return $this->output($link);
  }

  /**
   *
   * @return Simplify_HtmlElement
   */
  public function link($href, $label, $title = null, $attrs = array(), $params = array())
  {
    if (! sy_url_is_absolute($href)) {
      $href = $this->response->makeUrl($href);
    }

    $link = e('<a>', $attrs)->html($label)->attr('href', $href)->attr('title', $title ? $title : $label);

    return $this->output($link);
  }

}
