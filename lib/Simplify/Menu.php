<?php

namespace Simplify;

class Menu extends MenuItem
{

  protected $items = array();

  public function __construct($name, array $items = null, $label = null, $icon = null, $data = null)
  {
    parent::__construct($name, $label, $icon, null, null, $data);

    if (! empty($items)) {
      foreach ($items as $item) {
        $this->addItem($item);
      }
    }
  }

  public function items()
  {
    return $this->items;
  }

  public function addItem(MenuItem $item)
  {
    $this->items[] = $item;
    return $this;
  }

  public function addItemAt(MenuItem $item, $index)
  {
    array_splice($this->items, $index, 0, array($item));
    return $this;
  }

  public function numItems()
  {
    return count($this->items);
  }

  /**
   *
   * @return MenuItem
   */
  public function getItemAt($index)
  {
    return $this->items[$index];
  }

  /**
   * 
   * @param string $name
   * @throws MenuException
   * @return \Simplify\Menu
   */
  public function getItemByName($name)
  {
    $index = count($this->items) - 1;

    while ($index >= 0 && $this->items[$index]->name != $name)
      $index --;

    if ($index < 0) {
      throw new MenuException("Menu item not found: <b>$name</b>");
    }

    return $this->items[$index];
  }

  public function getItemIndex(MenuItem $item)
  {
    $index = count($this->items) - 1;

    while ($index >= 0 && $this->items[$index] === $item)
      $index --;

    if ($index < 0) {
      throw new MenuException("Menu item does not exist in menu");
    }

    return $index;
  }

}
