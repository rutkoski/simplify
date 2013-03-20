<?php

class Simplify_Cache
{

  protected static $handlers;

  public static function cached($id)
  {
    return self::getHandler()->cached($id);
  }

  public static function delete($id)
  {
    return self::getHandler()->delete($id);
  }

  public static function read($id)
  {
    return self::getHandler()->read($id);
  }

  public static function flush()
  {
    return self::getHandler()->flush();
  }

  public static function write($id, $data = '', $ttl = null)
  {
    return self::getHandler()->write($id, $data, $timeout);
  }

  public static function setHandler($type, Cache_CacheInterface $handler)
  {
    self::$handler[$type] = $handler;
  }

  /**
   *
   * @return Cache_CacheInterface
   */
  public static function getHandler($class = null)
  {
    if (!$class)
      $class = 'Simplify_Cache_File';

    if (empty(self::$handlers[$class])) {
      self::$handlers[$class] = new $class;
    }

    return self::$handlers[$class];
  }

}
