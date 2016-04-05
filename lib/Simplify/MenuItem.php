<?php

namespace Simplify;

class MenuItem
{

  /**
   *
   * @var string
   */
  public $name;

  /**
   *
   * @var string
   */
  public $label;

  /**
   *
   * @var string
   */
  public $icon;

  /**
   *
   * @var mixed
   */
  public $data;

  /**
   *
   * @var mixed
   */
  public $url;

  /**
   *
   * @var Menu
   */
  public $submenu;

  /**
   *
   * @var boolean
   */
  public $enabled = true;

  /**
   *
   * @param string $name
   * @param string $label
   * @param string $icon
   * @param URL $url
   * @param Menu $submenu
   * @param mixed $data
   */
  public function __construct($name, $label = null, $icon = null, $url = null, Menu $submenu = null, $data = null)
  {
    $this->name = $name;
    $this->label = $label;
    $this->icon = $icon;
    $this->data = $data;
    $this->url = URL::parse($url);
    $this->submenu = $submenu;
  }

  /**
   *
   * @param string $name
   * @param string $label
   * @param string $icon
   * @param URL $url
   * @param Menu $submenu
   * @return MenuItem
   */
  public static function factory($name, $label = null, $icon = null, $url = null, Menu $submenu = null)
  {
    return new self($name, $label, $icon, $url, $submenu);
  }

  public function isMenu()
  {
    return ($this instanceof Menu);
  }
  
  /**
   *
   * @param string $label
   * @return MenuItem
   */
  public function setLabel($label)
  {
    $this->label = $label;
    return $this;
  }

  /**
   *
   * @param string $icon
   * @return MenuItem
   */
  public function setIcon($icon)
  {
    $this->icon = $icon;
    return $this;
  }

  /**
   *
   * @param URL $url
   * @return MenuItem
   */
  public function setUrl($url)
  {
    $this->url = URL::parse($url);
    return $this;
  }

  /**
   *
   * @param Menu $submenu
   * @return MenuItem
   */
  public function setSubmenu(Menu $submenu)
  {
    $this->submenu = $submenu;
    return $this;
  }

}
