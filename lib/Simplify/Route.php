<?php

namespace Simplify;

class Route
{

  protected $uri;
  protected $options;

  protected $regex;
  protected $defaults;
  protected $patterns;
  protected $names;
  protected $required;

  public function __construct($uri, $options)
  {
    $this->uri = $uri;
    $this->options = $options;

    $this->prepareRoute();
  }

  public function getOptions()
  {
    return $this->options;
  }

  public function match($uri)
  {
    if (isset($this->options['method']) && ! preg_match('#^' . $this->options['method'] . '$#i', Simplify::request()->method())) {
      return false;
    }

    if (!preg_match($this->regex, $uri, $found)) {
      throw new RouterException("Route does not match uri: <b>{$uri}</b>");
    }

    array_shift($found);

    $params = array();

    foreach ($this->names as $i => $name) {
      $params[$name] = sy_get_param($found, $i, sy_get_param($this->defaults, $name));
    }

    return new MatchedRoute($this, $params, $uri);
  }

  public function make($params = null)
  {
    $params = array_merge((array) $this->defaults, (array) $params);

    $uri = $this->uri;

    foreach ($this->names as $name) {
      $value = sy_get_param($params, $name);

      if (! isset($params[$name])) {
        if (isset($this->required[$name])) {
          throw new RouterException("Missing required parameter <b>{$name}</b>");
        }
      }
      else {
        if (isset($this->patterns[$name]) && ! preg_match($this->patterns[$name], $value)) {
          throw new RouterException("Parameter <b>{$name}</b> does not match it's requirements");
        }
      }

      if ($name !== 'extra' && strpos($value, '/') !== 0) {
        $value = '/' . $value;
      }

      $uri = str_replace('/:' . $name, $value, $uri);
    }

    return $uri;
  }

  protected function prepareRoute()
  {
    $defaults = (array) sy_get_param($this->options, 'defaults');
    $patterns = (array) sy_get_param($this->options, 'patterns');

    $words = preg_split('#(/[^/]+)#', $this->uri, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

    $regex = array();
    $names = array();
    $required = array();

    $index = 0;

    foreach ($words as $i => $word) {
      if ($word == '/*') {
        $regex[] = '(?:(/.*))?';
        $patterns['extra'] = '#^(.*)?#';
        $names[$index++] = 'extra';
        $words[$i] = '/:extra';

        break;
      }
      elseif (strpos($word, '/:') === 0) {
        $wildcard = substr($word, 2);

        $names[$index++] = $wildcard;

        if (isset($patterns[$wildcard])) {
          if (isset($defaults[$wildcard]) || preg_match('#' . $patterns[$wildcard] . '#', '')) {
            $regex[] = '(?:/(' . $patterns[$wildcard] . '))?';
          }
          else {
            $regex[] = '/(' . $patterns[$wildcard] . ')';
            $required[] = $wildcard;
          }

          $patterns[$wildcard] = '#^' . $patterns[$wildcard] . '$#';
        }
        elseif (array_key_exists($wildcard, $defaults)) {
          $regex[] = '(?:/([^/]+))?';
          $patterns[$wildcard] = '#^([^/]+)?$#';
        }
        else {
          $required[] = $wildcard;
          $regex[] = '/([^/]+)';
          $patterns[$wildcard] = '#^[^/]+$#';
        }
      }
      else {
        $regex[] = $word;
      }
    }

    $regex = '#^' . implode('', $regex) . '$#';

    $this->uri = implode('', $words);
    $this->regex = $regex;
    $this->defaults = $defaults;
    $this->patterns = $patterns;
    $this->names = $names;
    $this->required = $required;
  }

}