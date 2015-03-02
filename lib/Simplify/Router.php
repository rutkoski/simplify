<?php

namespace Simplify;

use Simplify;

class Router
{

  protected $uri;
  protected $filters = array();
  protected $routes = array();
  protected $named = array();

  protected $building = false;

  public function make($name, $params = null)
  {
    if (!isset($this->named[$name])) {
      throw new RouterException("Unknown named route <b>{$name}</b>");
    }

    $route = $this->named[$name];

    $uri = $route->make($params);

    if (!$this->building) {
      $this->building = true;
      $uri = $this->buildFilters($uri);
      $this->building = false;
    }

    return $uri;
  }

  public function _parse($uri = null)
  {
    if (empty($uri)) {
      $uri = Simplify::request()->route();
    }

    $uri = $this->parseFilters($uri);

    $match = false;

    reset($this->routes);

    do {
      $route = current($this->routes);

      next($this->routes);
    }
    while ($route !== false && ! ($match = $route->match($uri)));

    return $match;
  }

  public function parse($uri = null)
  {
    if (empty($uri)) {
      $uri = Simplify::request()->route();
    }

    $uri = $this->parseFilters($uri);

    $match = false;

    reset($this->routes);

    do {
      $routes = current($this->routes);
      
      do {
        $route = current($routes);
      
        try {
          $match = $route->match($uri);
        }
        catch (RouterException $e) {
          //
        }
      }
      while ($route !== false && ! $match && next($routes) !== false);

      next($this->routes);
    }
    while ($routes !== false && ! $match);

    return $match;
  }
  
  public function filter($uri, $options = null)
  {
    $filter = new RouteFilter($uri, $options);

    $this->filters[] = $filter;

    if (isset($options['as'])) {
      $this->named[$options['as']] = $filter;
    }

    return $filter;
  }

  public function match($uri, $options = null)
  {
    $route = new Route($uri, $options);

    $priority = sy_get_param($options, 'priority', 0);

    if (!isset($this->routes[$priority])) {
      $this->routes[$priority] = array();
    }

    $this->routes[$priority][] = $route;

    if (isset($options['as'])) {
      $this->named[$options['as']] = $route;
    }

    return $route;
  }

  public function parseFilters($uri)
  {
    foreach ($this->filters as $filter) {
      $uri = $filter->parseRoute($uri);
    }

    return $uri;
  }

  public function buildFilters($uri)
  {
    for ($i = count($this->filters) - 1; $i >= 0; $i--) {
      $filter = $this->filters[$i];
      $uri = $filter->buildRoute($uri);
    }

    return $uri;
  }

}
