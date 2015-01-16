<?php

namespace Simplify;

class Dispatcher
{

  public static function execute($callback, $params)
  {
    return call_user_func_array($callback, self::sortCallbackParameters($callback, $params));
  }

  public static function sortCallbackParameters($callback, $params)
  {
    if (is_string($callback) || $callback instanceof \Closure) {
      $function = new \ReflectionFunction($callback);
    } elseif (method_exists($callback[0], $callback[1])) {
      $function = new \ReflectionMethod($callback[0], $callback[1]);
    } else {
      throw new \Exception('Invalid callback');
    }

    $funcParams = $function->getParameters();

    $sortedParams = array();

    foreach ($funcParams as $parameter) {
      $name = $parameter->name;

      if (array_key_exists($name, $params)) {
        $sortedParams[$name] = $params[$name];

        unset($params[$name]);
      }
      elseif ($parameter->isDefaultValueAvailable()) {
        $sortedParams[$name] = $parameter->getDefaultValue();
      }
      else {
        throw new \BadMethodCallException("Missing parameter <b>{$name}</b>");
      }
    }

    foreach ($params as $name => $param) {
      if (is_numeric($name)) {
        $sortedParams[$name] = $param;
      }
    }

    return $sortedParams;
  }

}
