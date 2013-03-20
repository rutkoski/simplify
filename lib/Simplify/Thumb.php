<?php

class Simplify_Thumb
{

  const TOP = 'T';
  const TOP_LEFT = 'TL';
  const TOP_RIGHT = 'TR';
  const BOTTOM = 'B';
  const BOTTOM_LEFT = 'BL';
  const BOTTOM_RIGHT = 'BR';
  const LEFT = 'L';
  const RIGHT = 'R';
  const CENTER = 'C';

  public $baseDir;

  public $filesPath;

  public $cachePath;

  protected $ignoreCache = false;

  protected $operations = array();

  protected $originalFile;

  protected $originalType;

  /**
   *
   * @return Thumb
   */
  public static function factory()
  {
    return new self();
  }

  /**
   * Constructor.
   *
   * @param IApplicationController $app
   * @return void
   */
  public function __construct()
  {
    $this->baseDir = s::config()->get('www_dir');
    $this->filesPath = s::config()->get('files_path');
    $this->cachePath = s::config()->get('files_path') . '/cache';
  }

  /**
   *
   * @return Thumb
   */
  public function ignoreCache($ignoreCache = true)
  {
    $this->ignoreCache = $ignoreCache;
    return $this;
  }

  /**
   *
   * @return Thumb
   */
  public function load($file)
  {
    $this->originalFile = $file;
    if (! sy_path_is_absolute($file)) {
      $file = $this->baseDir . $this->filesPath . DIRECTORY_SEPARATOR . $file;
    }
    $info = @getimagesize($file);
    $this->originalType = $info[2];
    return $this;
  }

  /**
   *
   * @return Thumb
   */
  public function resize($width = null, $height = null, $proportional = true, $fitInside = true, $far = false, $background = 0xffffff)
  {
    $params = func_get_args();
    array_unshift($params, 'Simplify_Thumb_Plugin_Resize');
    $this->operations[] = array('callPlugin', $params);
    return $this;
  }

  /**
   *
   * @return Thumb
   */
  public function crop($x, $y, $width, $height)
  {
    $params = func_get_args();
    array_unshift($params, 'Simplify_Thumb_Plugin_Crop');
    $this->operations[] = array('callPlugin', $params);
    return $this;
  }

  /**
   *
   * @return Thumb
   */
  public function zoomCrop($width = null, $height = null, $gravity = Thumb::CENTER)
  {
    $params = func_get_args();
    array_unshift($params, 'Simplify_Thumb_Plugin_ZoomCrop');
    $this->operations[] = array('callPlugin', $params);
    return $this;
  }

  /**
   *
   * @return Thumb
   */
  public function quality($quality)
  {
    $this->operations[] = array('quality', func_get_args());
    return $this;
  }

  /**
   *
   * @return Thumb
   */
  public function brightness($level)
  {
    $params = func_get_args();
    array_unshift($params, 'Simplify_Thumb_Plugin_Brightness');
    $this->operations[] = array('callPlugin', $params);
    return $this;
  }

  /**
   *
   * @return Thumb
   */
  public function contrast($level)
  {
    $params = func_get_args();
    array_unshift($params, 'Simplify_Thumb_Plugin_Contrast');
    $this->operations[] = array('callPlugin', $params);
    return $this;
  }

  /**
   *
   * @return Thumb
   */
  public function plugin($plugin)
  {
    $params = func_get_args();
    $this->operations[] = array('callPlugin', $params);
    return $this;
  }

  /**
   * Save the image.
   *
   * @return string
   */
  public function save($file = null)
  {
    if (empty($file)) {
      $file = $this->originalFile;
    }

    if (! sy_path_is_absolute($file)) {
      $file = $this->baseDir . $this->filesPath . DIRECTORY_SEPARATOR . $file;
    }

    $cacheFilename = $this->getCacheFilename();

    if (file_exists($cacheFilename) && ! $this->ignoreCache) {
      copy($cacheFilename, $file);
    } else {
      $this->process()->save($file);
    }

    return $file;
  }

  /**
   * Output the image to the browser.
   *
   * @return void
   */
  public function output($type = null, $cacheSeconds = 604800)
  {
    if (file_exists($cacheFilename) && ! $this->ignoreCache) {
      $this->outputFromCache($type, $cacheSeconds);
    } else {
      $this->process()->output($type, $cacheSeconds);
    }
  }

  /**
   * Cache and output the image to browser.
   *
   * @return void
   */
  public function outputFromCache($type = null, $cacheSeconds = 604800)
  {
    if (empty($type)) {
      $type = $this->originalType;
    }

    if ($cacheSeconds) {
      header("Cache-Control: private, max-age={$cacheSeconds}, pre-check={$cacheSeconds}");
      header("Expires: " . date(DATE_RFC822, strtotime("{$cacheSeconds} seconds")));
      header("Pragma: private");
    } else {
      header("Pragma: no-cache");
    }

    header('Content-Type: ' . $this->getImageMimeType($type));

    readfile($this->baseDir . $this->cache($type)->getCacheFilename());

    exit();
  }

  /**
   * Cache the image.
   *
   * @return Thumb
   */
  public function cache($type = null)
  {
    if (empty($type)) {
      $type = $this->originalType;
    }

    $cacheFilename = $this->getCacheFilename($type);

    $filename = $this->baseDir . $cacheFilename;

    if (! file_exists($filename) || $this->ignoreCache) {
      $this->process()->save($filename, $type);
    }

    return $this;
  }

  /**
   * Get image cache filename.
   *
   * @return string
   */
  public function getCacheFilename($type = null)
  {
    if (empty($type)) {
      $type = $this->originalType;
    }

    $filename = $this->cachePath . '/' . $this->getCachePrefix() . md5(serialize($this->operations)) . image_type_to_extension($type);

    return $filename;
  }

  public function cleanCached()
  {
    foreach (glob($this->baseDir . $this->cachePath . '/' . $this->getCachePrefix() . '*.*') as $file) {
      @unlink($file);
    }

    return $this;
  }

  protected function getImageMimeType($type = null)
  {
    if (empty($type)) {
      $type = $this->originalType;
    }
    return image_type_to_mime_type($type);
  }

  protected function getCachePrefix()
  {
    return 'thumbcache_' . md5($this->originalFile) . '_';
  }

  /**
   * Process the image.
   *
   * @return ThumbProcessor
   */
  protected function process()
  {
    $file = $this->originalFile;

    if (! sy_path_is_absolute($file)) {
      $file = $this->baseDir . $this->filesPath . DIRECTORY_SEPARATOR . $file;
    }

    $f = new Simplify_Thumb_Processor;

    $f->load($file);

    foreach ($this->operations as $op) {
      call_user_func_array(array($f, $op[0]), $op[1]);
    }

    return $f;
  }

}
