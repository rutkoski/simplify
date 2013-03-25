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
 * Object that represents an HTML element
 *
 */
class Simplify_HtmlElement
{

  /**
   *
   * @var Simplify_HtmlElement
   */
  protected $parent;

  protected $name;

  protected $attrs = array();

  protected $children = array();

  /**
   *
   * @var array
   */
  public static $closedElements = array('img', 'input', 'button', 'hr', 'link', 'meta', 'br');

  /**
   * Constructor.
   *
   * @return void
   */
  public function __construct($e, $attrs = array())
  {
    if (preg_match('/^<([a-z]+)>$/', $e, $o)) {
      $this->name = $o[1];
    }
    else {
      $this->html($e);
    }
    $this->attr($attrs);
  }

  /**
   *
   * @return Simplify_HtmlElement|mixed
   */
  public function data($name, $value = null)
  {
    if (func_num_args() == 1) {
      return $this->attr("data-{$name}");
    }
    else {
      return $this->attr("data-{$name}", $value);
    }
  }

  /**
   *
   * @return Simplify_HtmlElement
   */
  public function addClass($class)
  {
    if (($classes = $this->attr('class')) !== false) {
      $classes = (array) explode(' ', $classes);
      array_push($classes, $class);
      $class = implode(' ', $classes);
    }
    $this->attr('class', $class);
    return $this;
  }

  /**
   *
   * @return boolean
   */
  public function hasClass($class)
  {
    if (($classes = $this->attr('class')) !== false) {
      $classes = explode(' ', $classes);
      if (($i = array_search($class, $classes)) !== false) {
        return true;
      }
    }
    return false;
  }

  /**
   *
   * @return Simplify_HtmlElement
   */
  public function removeClass($class)
  {
    if (($classes = $this->attr('class')) !== false) {
      $classes = explode(' ', $classes);
      if (($i = array_search($class, $classes)) !== false) {
        $classes = array_splice($classes, $i, 1);
        $this->attr('class', $class);
      }
    }
    return $this;
  }

  /**
   *
   * @return Simplify_HtmlElement
   */
  public function attr($attr, $value = null)
  {
    if (is_array($attr)) {
      foreach ($attr as $name => $value) {
        $this->attrs[$name] = $value;
      }
    }
    elseif (is_null($value)) {
      return isset($this->attrs[$attr]) ? $this->attrs[$attr] : false;
    }
    else {
      $this->attrs[$attr] = $value;
    }
    return $this;
  }

  /**
   *
   * @return Simplify_HtmlElement
   */
  public function after($e)
  {
    $e = e($e);
    if (empty($this->parent)) {
      $this->parent = e()->append($this)->append($e);
    }
    elseif (($i = $this->index()) !== false) {
      array_splice($this->parent->children, $i, 0, array($e));
      $e->parent = $this->parent;
    }
    return $this;
  }

  /**
   *
   * @return Simplify_HtmlElement
   */
  public function wrap($e)
  {
    $e = e($e);
    if (empty($this->parent)) {
      $e->append($this);
    }
    else {
      $e->append($this->after($e));
    }
    return $this;
  }

  /**
   *
   * @return Simplify_HtmlElement
   */
  public function remove()
  {
    if ($this->parent instanceof Simplify_HtmlElement) {
      if (($i = $this->index()) !== false) {
        $e = $this->parent->children[$i];
        array_splice($this->parent->children, $i, 1);
        $e->parent = null;
      }
    }
    return $this;
  }

  /**
   *
   * @return int|bool
   */
  public function index($e = null)
  {
    if ($e) {
      return array_search($e, $this->children);
    }
    elseif ($this->parent) {
      return array_search($this, $this->parent->children);
    }
    return false;
  }

  /**
   *
   * @return Simplify_HtmlElement
   */
  public function append($e)
  {
    $_e = is_array($e) ? $e : array($e);
    
    foreach ($_e as $e) {
      $e = e($e)->remove();
      $e->parent = $this;
      array_push($this->children, $e);
    }
    return $this;
  }

  /**
   *
   * @return Simplify_HtmlElement
   */
  public function prepend($e)
  {
    $e = e($e)->remove();
    $e->parent = $this;
    array_unshift($this->children, $e);
    return $this;
  }

  /**
   *
   * @return Simplify_HtmlElement
   */
  public function parent()
  {
    return $this->parent;
  }

  /**
   *
   * @return Simplify_HtmlElement|string
   */
  public function html($e = null)
  {
    if (is_null($e)) {
      $s = '';
      
      foreach ($this->children as $child) {
        if ($child instanceof Simplify_HtmlElement) {
          $s .= $child->render();
        }
        else { //if (is_string($child)) {
          $s .= $child;
        }
      }
      
      return $s;
    }
    else {
      $this->children = array($e);
      
      return $this;
    }
  }

  /**
   *
   * @return string
   */
  public function __toString()
  {
    return $this->render();
  }

  protected function render()
  {
    $s = '';
    
    $tag = $this->name;
    
    $html = $this->html();
    
    $attrs = array();
    foreach ($this->attrs as $name => $value) {
      $attrs[] = $name . '="' . $value . '"';
    }
    $attrs = empty($attrs) ? '' : ' ' . implode(' ', $attrs);
    
    if ($tag) {
      if (in_array($tag, self::$closedElements)) {
        $s .= "<{$tag}{$attrs}/>";
      }
      else {
        $s .= "<{$tag}{$attrs}>{$html}</{$tag}>";
      }
    }
    else {
      $s = $html;
    }
    
    return $s;
  }

}
