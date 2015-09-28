<?php

namespace Simplify;

use MatthiasMullie\Minify;

class AssetManager
{

  const CSS = '.css';

  const JS = '.js';

  const UNKNOWN = 'unknown';

  protected static $assets = array();

  public static function getAssetType ($asset)
  {
    switch (substr($asset, strrpos($asset, '.'))) {
      case '.css':
        return self::CSS;
      case '.js':
        return self::JS;
      default:
        return self::UNKNOWN;
    }
  }
  
  public static function assets ($groups = null, $join = '')
  {
    $minify = \Simplify::config()->get('assets:minify', false, \Simplify\Dictionary::FILTER_NULL);
    
    $output = array();
    
    $groups = (array) $groups;
    
    $assets = self::$assets;
    $keys = array_keys($assets);
    
    uasort($assets, 
        function  ($a, $b) use( $groups, $keys)
        {
          if (array_search($a['group'], $groups) < array_search($b['group'], $groups)) {
            return - 1;
          }
          
          if ($a['group'] == $b['group']) {
            if ($a['priority'] < $b['priority']) {
              return - 1;
            }
            elseif ($a['priority'] == $b['priority'] && array_search($a['asset'], $keys) < array_search($b['asset'], $keys)) {
              return -1;
            }
          }
          
          return 1;
        });
    
    if ($minify) {
      $css = array();
      $cssPath = array();
      
      $js = array();
      $jsPath = array();
    }
    
    foreach ($assets as $params) {
      if (! sy_get_param($params, 'output')) {
        if ($minify && sy_get_param($params, 'minify')) {
          $dir = $params['dir'];
          
          switch (self::getAssetType($dir)) {
            case self::CSS:
              if (! isset($css[$params['group']])) {
                $css[$params['group']] = new Minify\CSS();
                $cssPath[$params['group']] = '';
              }
              
              $cssPath[$params['group']] .= '_' . $dir . '_' . filemtime($dir);
              
              $css[$params['group']]->add($dir);
              
              break;
            
            case self::JS:
              if (! isset($js[$params['group']])) {
                $js[$params['group']] = new Minify\JS();
                $jsPath[$params['group']] = '';
              }
              
              $jsPath[$params['group']] .= '_' . $dir . '_' . filemtime($dir);
              
              $js[$params['group']]->add($dir);
              
              break;
          }
        }
        else {
          $output[$params['group']][] = self::output($params['asset']);
        }
      }
    }
    
    if ($minify) {
      $cacheDir = \Simplify::config()->get('cache:dir');
      $cacheUrl = \Simplify::config()->get('cache:url');

      \Simplify\File::createDir($cacheDir);
      
      foreach ($cssPath as $group => $_cssPath) {
        $path = $group . '_' . md5($_cssPath) . '.min.css';
        
        if (! file_exists($cacheDir . $path)) {
          $css[$group]->minify($cacheDir . $path);
        }
        
        if (! isset($output[$group])) {
          $output[$group] = array();
        }
        
        array_unshift($output[$group], "<link rel=\"stylesheet\" href=\"" . $cacheUrl . $path . "\" />");
      }
      
      foreach ($jsPath as $group => $_jsPath) {
        $path = $group . '_' . md5($_jsPath) . '.min.js';
        
        if (! file_exists($cacheDir . $path)) {
          $js[$group]->minify($cacheDir . $path);
        }
        
        if (! isset($output[$group])) {
          $output[$group] = array();
        }
        
        array_unshift($output[$group], "<script src=\"" . $cacheUrl . $path . "\"></script>");
      }
    }
    
    if ($join !== false) {
      $_output = '';
      foreach ($output as $group => $assets) {
        $_output .= $join . implode($join, $assets);
      }
      $output = $_output;
    }
    
    return $output;
  }

  public static function load ($asset, $group = null, $priority = 0, $minify = true)
  {
    self::loadAsset($asset, $group, $priority, $minify);
  }

  public static function asset ($asset, $group = null, $priority = 0)
  {
    switch (self::getAssetType($asset)) {
      case self::CSS:
      case self::JS:
        return self::load($asset, $group, $priority);
    }
    
    return self::output(self::loadAsset($asset, $group), $group, $priority);
  }

  public static function loadAsset ($asset, $group = null, $priority = 0, $minify = true)
  {
    if (empty($asset)) {
      throw new \Exception("Empty asset name");
    }
    
    if (empty(self::$assets[$asset])) {
      $paths = \Simplify::config()->get('app:assets:path');
      
      do {
        $path = array_shift($paths);
        $dir = \Simplify::config()->resolveReferences('{www:dir}' . $path);
      }
      while (! empty($paths) && ! file_exists($dir . $asset));
      
      if (! file_exists($dir . $asset)) {
        throw new \Exception("Asset file not found: {$asset}");
      }
      
      $url = \Simplify::config()->resolveReferences('{www:url}' . $path);
      
      self::$assets[$asset] = array(
          'asset' => $asset,
          'dir' => $dir . $asset,
          'url' => $url . $asset,
          'group' => $group,
          'priority' => $priority,
          'minify' => $minify
      );
    }
    
    return $asset;
  }

  protected static function output ($asset, $group = null)
  {
    $url = self::$assets[$asset]['url'];
    $output = sy_get_param(self::$assets[$asset], 'output');
    
    switch (substr($url, strrpos($url, '.'))) {
      case '.css':
        if (empty($output)) {
          $output = "<link rel=\"stylesheet\" href=\"{$url}\" />";
        }
        break;
      
      case '.js':
        if (empty($output)) {
          $output = "<script src=\"{$url}\"></script>";
        }
        break;
      
      default:
        $output = $url;
    }
    
    self::$assets[$asset]['output'] = true;
    
    return $output;
  }
}