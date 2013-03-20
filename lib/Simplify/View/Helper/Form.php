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
 * @author Rodrigo Rutkoski Rodrigues <rutkoski@gmail.com>
 *
 */
class Simplify_View_Helper_Form extends Simplify_View_Helper
{

  /**
   *
   * @return Simplify_HtmlElement
   */
  public function submit($label, $attrs = array(), $params = array())
  {
    return $this->output(e('<input>', $attrs)->attr('type', 'submit')->attr('value', $label));
  }

  /**
   *
   * @return Simplify_HtmlElement
   */
  public function text($name, $value = null, $attrs = array(), $params = array())
  {
    return $this->output(e('<input>', $attrs)->attr('type', 'text')->attr('name', $name)->attr('value', $value));
  }

  /**
   *
   * @return Simplify_HtmlElement
   */
  public function textarea($name, $value = null, $attrs = array(), $params = array())
  {
    return $this->output(e('<textarea>', $attrs)->attr('name', $name)->html($value));
  }

  /**
   *
   * @return Simplify_HtmlElement
   */
  public function hidden($name, $value = null, $attrs = array(), $params = array())
  {
    return $this->output(e('<input>', $attrs)->attr('type', 'hidden')->attr('name', $name)->attr('value', $value));
  }

  /**
   *
   * @return Simplify_HtmlElement
   */
  public function select($name, $data, $selectedKey = null, $attrs = array(), $params = array())
  {
    return $this->output(e('<select>', $attrs)->attr('name', $name)->append($this->options($data, $selectedKey, null, $params)));
  }

  /**
   *
   * @return Simplify_HtmlElement
   */
  public function option($value = null, $label = null, $selected = null, $attrs = array(), $params = array())
  {
    if ($selected) {
      $attrs['selected'] = 'selected';
    } else {
      unset($attrs['selected']);
    }

    return $this->output(e('<option>', $attrs)->attr('value', $value)->html($label));
  }

  /**
   *
   * @return Simplify_HtmlElement
   */
  public function options($data, $selectedKey = null, $attrs = array(), $params = array())
  {
    $output = e();

    foreach ((array) $data as $value => $label) {
      if (count($output)) {
        $output->append(sy_get_param($params, 'itemSeparator'));
      }

      $option = $this->option($value, $label, (string) $value == (string) $selectedKey, $attrs, $params);

      $output->append($option);
    }

    return $this->output($output);
  }

  /**
   *
   * @return Simplify_HtmlElement
   */
  public function checkbox($name, $value = null, $checked = null, $attrs = array(), $params = array())
  {
    if ($checked) {
      $attrs['checked'] = 'checked';
    } else {
      unset($attrs['checked']);
    }

    return $this->output(e('<input>', $attrs)->attr('type', 'checkbox')->attr('name', $name)->attr('value', $value));
  }

  /**
   *
   * @return Simplify_HtmlElement
   */
  public function checkboxes($name, $data, $checkedKeys = array(), $attrs = array(), $params = array())
  {
    $output = e();

    foreach ((array) $data as $value => $label) {
      if (count($output)) {
        $output->append(sy_get_param($params, 'itemSeparator'));
      }

      $option = $this->checkbox($name, $value, in_array($value, $checkedKeys), $attrs, $params);

      $output->append($option)->append(' ' . $label);
    }

    return $this->output($output);
  }

  /**
   *
   * @return Simplify_HtmlElement
   */
  public function radio($name, $value = null, $checked = null, $attrs = array(), $params = array())
  {
    if ($checked) {
      $attrs['checked'] = 'checked';
    } else {
      unset($attrs['checked']);
    }

    return $this->output(e('<input>', $attrs)->attr('type', 'radio')->attr('name', $name)->attr('value', $value));
  }

  /**
   *
   * @return Simplify_HtmlElement
   */
  public function radios($name, $data, $selectedKey = null, $attrs = array(), $params = array())
  {
    $output = e();

    foreach ((array) $data as $value => $label) {
      if (count($output)) {
        $output->append(sy_get_param($params, 'itemSeparator'));
      }

      $option = $this->radio($name, $value, $value == $selectedKey, $attrs, $params);

      $output->append($option)->append(' ' . $label);
    }

    return $this->output($output);
  }

}
