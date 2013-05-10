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
 * Image processor
 *
 */
class Simplify_Thumb_Processor
{

  /**
   * Default jpg quality
   *
   * @var int
   */
  public $quality = 99;

  /**
   * Image resource
   *
   * @var resource
   */
  protected $image;

  /**
   * Call a plugin
   *
   * @param string $name
   */
  public function callPlugin($name)
  {
    $params = func_get_args();

    array_shift($params);

    $class = $name;

    $plugin = new $class;

    call_user_func_array(array($plugin, 'process'), array_merge(array($this), $params));
  }

  /**
   * Set jpg quality
   *
   * @param int $q
   */
  public function quality($q)
  {
    $this->quality = $q;
  }

  /**
   * Load image file
   *
   * @param string $file
   * @throws Simplify_ThumbException
   */
  public function load($file)
  {
    if (! file_exists($file) || ! is_file($file)) {
      throw new Simplify_ThumbException("File not found: <b>{$file}</b>");
    }

    $info = getimagesize($file);

    $originalType = $info[2];

    $image = null;

    switch ($originalType) {
      case IMAGETYPE_JPEG:
        $image = imagecreatefromjpeg($file);
        break;

      case IMAGETYPE_GIF:
        $image = imagecreatefromgif($file);
        break;

      case IMAGETYPE_PNG:
        $image = imagecreatefrompng($file);
        break;
    }

    Simplify_Thumb_Functions::validateImageResource($image);

    $this->image = $image;
    $this->originalType = $originalType;
  }

  /**
   * Output image
   *
   * @param string $type image type
   * @param int $cacheSeconds cache time in seconds
   */
  public function output($type = null, $cacheSeconds = 604800)
  {
    $image = $this->image;

    Simplify_Thumb_Functions::validateImageResource($image);

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

    switch ($type) {
      case IMAGETYPE_JPEG:
        imagejpeg($this->image, null, $this->quality);
        break;

      case IMAGETYPE_GIF:
        imagegif($this->image);
        break;

      case IMAGETYPE_PNG:
        imagepng($this->image);
        break;
    }

    exit();
  }

  /**
   * Save file
   *
   * @param string $file
   * @param string $type
   */
  public function save($file, $type = null)
  {
    $image = $this->image;

    if (empty($type)) {
      $type = $this->originalType;
    }

    Simplify_Thumb_Functions::validateImageResource($image);

    if (empty($type)) {
      $type = IMAGETYPE_JPEG;
    }

    switch ($type) {
      case IMAGETYPE_JPEG:
        imagejpeg($image, $file, $this->quality);
        break;

      case IMAGETYPE_GIF:
        imagesavealpha($image, true);
        imagegif($image, $file);
        break;

      case IMAGETYPE_PNG:
        imagesavealpha($image, true);
        imagepng($image, $file);
        break;
    }
  }

  /**
   * Get mime type
   *
   * @param string $type
   * @return string
   */
  public function getImageMimeType($type = null)
  {
    if (empty($type)) {
      $type = $this->originalType;
    }
    return image_type_to_mime_type($type);
  }

}
