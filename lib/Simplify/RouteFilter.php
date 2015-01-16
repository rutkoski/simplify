<?php

namespace Simplify;

class RouteFilter extends Route
{

  protected $parse;

  protected $build;

  public function parse($callback)
  {
    $this->parse = $callback;
    return $this;
  }

  public function build($callback)
  {
    $this->build = $callback;
    return $this;
  }

  public function parseRoute($uri)
  {
    if (false !== ($match = $this->match($uri))) {
      try {
        $uri = Dispatcher::execute($this->parse, $match->params);
      }
      catch (\Exception $e) {
        //
      }
    }

    return $uri;
  }

  public function buildRoute($uri, $params = null)
  {
    try {
      $params = (array) $params;

      $params['uri'] = $uri;

      $uri = Dispatcher::execute($this->build, $params);
    }
    catch (\Exception $e) {
      //
    }

    return $uri;
  }

}