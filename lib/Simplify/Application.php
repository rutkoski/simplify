<?php

namespace Simplify;

class Application
{

  public function dispatch()
  {
    $this->initialize();
    
    $match = $this->parseRoute();

    if (! $match) {
      return $this->pageNotFound();
    }

    $output = $this->forward($match);

    $this->outputResponse($output);

    return $output;
  }

  /**
   * 
   */
  protected function initialize()
  {
    \Simplify::session()->start();
  }

  /**
   *
   */
  protected function pageNotFound()
  {
    \Simplify::response()->set404();
   
    $output = false;

    $match = \Simplify::router()->parse('/page_not_found');

    if ($match) {
      $output = $this->forward($match);
    }
    
    return $this->outputResponse($output);
  }

  /**
   * 
   * @param unknown_type $output
   * @return string
   */
  protected function outputResponse($output)
  {
    return \Simplify::response()->output($output);
  }

  /**
   * 
   * @return \Simplify\MatchedRoute
   */
  protected function parseRoute()
  {
    return \Simplify::router()->parse();
  }

  /**
   * 
   * @param \Simplify\MatchedRoute $match
   * @throws \Exception
   * @return unknown
   */
  protected function forward(\Simplify\MatchedRoute $match)
  {
    $options = $match->route->getOptions();
    
    if (! isset($options['controller'])) {
      throw new \Exception('No controller defined in route');
    }
    
    $controller = sy_get_param($match->params, 'controller', $options['controller']);
    
    $action = sy_get_param($match->params, 'action', sy_get_param($options, 'action', 'index'));
    
    $Controller = new $controller;
    
    $output = $Controller->callAction($action, $match->params);
    
    return $output;
  }

}