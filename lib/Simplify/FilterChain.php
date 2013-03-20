<?php

class Simplify_FilterChain
{

  protected $filters = array();

  public function add($chain, $callback)
  {
    $args = func_get_args();
    unset($args[0], $args[1]);

    $this->filters[$chain][] = array($callback, $args);
    return $this;
  }

  public function call($chain, $output)
  {
    if (empty($this->filters[$chain])) {
      return $output;
    }

    $args = func_get_args();
    unset($args[0], $args[1]);

    $filters = $this->filters[$chain];

    foreach ($filters as $filter) {
      $callback = $filter[0];
      $params = $filter[1];

      $params = array($output) + $params + $args;

      $output = call_user_func_array($callback, $params);
    }

    return $output;
  }

}
