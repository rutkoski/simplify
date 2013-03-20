<?php

class Simplify_Data_ViewIterator implements Iterator
{

  protected $data;

  protected $index = 0;

  protected $count;

  /**
   *
   * @var Data_View
   */
  protected $view;

  /**
   *
   * @param array $data
   * @param string $view
   */
  public function __construct(&$data, $view = 'Simplify_Data_View')
  {
    $this->data = &$data;

    if (! ($view instanceof Simplify_Data_View)) {
      $view = new $view();
    }

    $this->view = $view;

    $this->count = count($this->data);
  }

  public function rewind()
  {
    $this->index = 0;
    $this->view->setData($this->data[$this->index]);
  }

  /**
   * (non-PHPdoc)
   * @see Iterator::current()
   * @return Data_View
   */
  public function current()
  {
    return $this->view;
  }

  public function key()
  {
    return $this->index;
  }

  public function next()
  {
    $this->index ++;
    $this->view->setData($this->data[$this->index]);
  }

  public function valid()
  {
    return ($this->index >= 0 && $this->index < $this->count);
  }

}
