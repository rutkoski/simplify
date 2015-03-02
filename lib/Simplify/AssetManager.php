<?php

namespace Simplify;

class AssetManager
{

  protected static $assets = array('style' => array(), 'javascript' => array());

  public static function style($path = null, $group = 'default')
  {
    if (!empty($path)) {
      if (!in_array($path, (array) sy_get_param(self::$assets['style'], $group))) {
        self::$assets['style'][$group][] = $path;
      }
    }

    return (array) sy_get_param(self::$assets['style'], $group);
  }

  public static function javascript($path = null, $group = 'default')
  {
    if (!empty($path)) {
      if (!in_array($path, (array) sy_get_param(self::$assets['javascript'], $group))) {
        self::$assets['javascript'][$group][] = $path;
      }
    }

    return (array) sy_get_param(self::$assets['javascript'], $group);
  }

}