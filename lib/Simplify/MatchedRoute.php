<?php

namespace Simplify;

class MatchedRoute
{

  public $route;
  public $params;
  public $uri;

  public function __construct($route, $params, $uri)
  {
    $this->uri = $uri;
    $this->params = $params;
    $this->route = $route;
  }

  public function execute($callback)
  {
    return Dispatcher::execute($callback, $this->params);
  }

}