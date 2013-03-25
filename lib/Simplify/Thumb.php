<?php

/**
 * SimplifyPHP Framework
 *
 * This file is part of SimplifyPHP Framework.
 *
 * SimplifyPHP Framework is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * SimplifyPHP Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Rodrigo Rutkoski Rodrigues <rutkoski@gmail.com>
 */

/**
 * 
 * Image processing
 *
 */
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
   * Instantiate a new instance of Simplify_Thumb
   *
   * @return Simplify_Thumb
   */
  public static function factory()
  {
    return new self();
  }

  /**
   * Constructor
   *
   * @return void
   */
  public function __construct()
  {
    $this->baseDir = s::config()->get('www_dir');
    $this->filesPath = s::config()->get('files_path');
    $this->cachePath = s::config()->get('files_path') . '/cache';
  }

  /**
   * Ignore cached files
   * 
   * @param boolean $ignoreCache
   * @return Simplify_Thumb
   */
  public function ignoreCache($ignoreCache = true)
  {
    $this->ignoreCache = $ignoreCache;
    return $this;
  }

  /**
   * Load image file
   * 
   * @param string $file
   * @return Simplify_Thumb
   */
  public function load($file)
  {
    $this->originalFile = $file;
    if (!sy_path_is_absolute($file)) {
      $file = $this->baseDir . $this->filesPath . DIRECTORY_SEPARATOR . $file;
    }
    $info = @getimagesize($file);
    $this->originalType = $info[2];
    return $this;
  }

  /**
   * Resize image
   * 
   * @param int $width reference output width
   * @param int $height reference output height
   * @param bool $proportional keep original proportion
   * @param bool $fitInside fit image inside (true) or outside (false) $width and $height
   * @param bool $far output exactly $width and $height and fill empty space with $background color
   * @param int $background background color or transparent (false)
   * @return Simplify_Thumb
   */
  public function resize($width = null, $height = null, $proportional = true, $fitInside = true, $far = false, $background = 0xffffff)
  {
    $params = func_get_args();
    array_unshift($params, 'Simplify_Thumb_Plugin_Resize');
    $this->operations[] = array('callPlugin', $params);
    return $this;
  }

  /**
   * Crop image
   * 
   * @param int $x crop top position
   * @param int $y crop left position
   * @param int $width crop width
   * @param int $height crop height
   * @return Simplify_Thumb
   */
  public function crop($x, $y, $width, $height)
  {
    $params = func_get_args();
    array_unshift($params, 'Simplify_Thumb_Plugin_Crop');
    $this->operations[] = array('callPlugin', $params);
    return $this;
  }

  /**
   * Zoom and crop image
   * 
   * @param int $width final width
   * @param int $height final height
   * @param string $gravity position position
   * @return Simplify_Thumb
   */
  public function zoomCrop($width = null, $height = null, $gravity = Simplify_Thumb::CENTER)
  {
    $params = func_get_args();
    array_unshift($params, 'Simplify_Thumb_Plugin_ZoomCrop');
    $this->operations[] = array('callPlugin', $params);
    return $this;
  }

  /**
   * Set jpg quality
   * 
   * @param int $quality jpg ouput quality (1 - 100)
   * @return Simplify_Thumb
   */
  public function quality($quality)
  {
    $this->operations[] = array('quality', func_get_args());
    return $this;
  }

  /**
   * Change image brightness level
   * 
   * @param int $level -255 = min brightness, 0 = no change, +255 = max brightness
   * @return Simplify_Thumb
   */
  public function brightness($level)
  {
    $params = func_get_args();
    array_unshift($params, 'Simplify_Thumb_Plugin_Brightness');
    $this->operations[] = array('callPlugin', $params);
    return $this;
  }

  /**
   * Change image contrast level
   * 
   * @param int $level -100 = max contrast, 0 = no change, +100 = min contrast
   * @return Simplify_Thumb
   */
  public function contrast($level)
  {
    $params = func_get_args();
    array_unshift($params, 'Simplify_Thumb_Plugin_Contrast');
    $this->operations[] = array('callPlugin', $params);
    return $this;
  }

  /**
   * Call plugin
   * 
   * @param string $plugin plugin class
   * @return Simplify_Thumb
   */
  public function plugin($plugin)
  {
    $params = func_get_args();
    $this->operations[] = array('callPlugin', $params);
    return $this;
  }

  /**
   * Save the image
   * 
   * @param string $file output filename
   * @return string
   */
  public function save($file = null)
  {
    if (empty($file)) {
      $file = $this->originalFile;
    }
    
    if (!sy_path_is_absolute($file)) {
      $file = $this->baseDir . $this->filesPath . DIRECTORY_SEPARATOR . $file;
    }
    
    $cacheFilename = $this->getCacheFilename();
    
    if (file_exists($cacheFilename) && !$this->ignoreCache) {
      copy($cacheFilename, $file);
    }
    else {
      $this->process()->save($file);
    }
    
    return $file;
  }

  /**
   * Output the image to the browser
   * 
   * @param string $type image type
   * @param int $cacheSeconds cache time in seconds
   */
  public function output($type = null, $cacheSeconds = 604800)
  {
    $cacheFilename = $this->getCacheFilename();
    
    if (file_exists($cacheFilename) && !$this->ignoreCache) {
      $this->outputFromCache($type, $cacheSeconds);
    }
    else {
      $this->process()->output($type, $cacheSeconds);
    }
  }

  /**
   * Cache and output the image to browser
   * 
   * @param string $type image type
   * @param int $cacheSeconds cache time in seconds
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
    }
    else {
      header("Pragma: no-cache");
    }
    
    header('Content-Type: ' . $this->getImageMimeType($type));
    
    readfile($this->baseDir . $this->cache($type)->getCacheFilename());
    
    exit();
  }

  /**
   * Process and cache the image
   * 
   * @param string $type image type
   * @return Simplify_Thumb
   */
  public function cache($type = null)
  {
    if (empty($type)) {
      $type = $this->originalType;
    }
    
    $cacheFilename = $this->getCacheFilename($type);
    
    $filename = $this->baseDir . $cacheFilename;
    
    if (!file_exists($filename) || $this->ignoreCache) {
      $this->process()->save($filename, $type);
    }
    
    return $this;
  }

  /**
   * Get image cache filename
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

  /**
   * Delete image cache
   * 
   * @return Simplify_Thumb
   */
  public function cleanCached()
  {
    foreach (glob($this->baseDir . $this->cachePath . '/' . $this->getCachePrefix() . '*.*') as $file) {
      @unlink($file);
    }
    
    return $this;
  }

  /**
   * Get mime type for image type
   * 
   * @param string $type image type
   * @return string
   */
  protected function getImageMimeType($type = null)
  {
    if (empty($type)) {
      $type = $this->originalType;
    }
    return image_type_to_mime_type($type);
  }

  /**
   * Get image cache prefix
   * 
   * @return string
   */
  protected function getCachePrefix()
  {
    return 'thumbcache_' . md5($this->originalFile) . '_';
  }

  /**
   * Process the image
   * 
   * @return Simplify_Thumb_Processor
   */
  protected function process()
  {
    $file = $this->originalFile;
    
    if (!sy_path_is_absolute($file)) {
      $file = $this->baseDir . $this->filesPath . DIRECTORY_SEPARATOR . $file;
    }
    
    $f = new Simplify_Thumb_Processor();
    
    $f->load($file);
    
    foreach ($this->operations as $op) {
      call_user_func_array(array($f, $op[0]), $op[1]);
    }
    
    return $f;
  }

}
