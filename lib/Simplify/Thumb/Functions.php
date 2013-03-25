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
 * Basic image operations
 *
 */
class Simplify_Thumb_Functions
{

  /**
   * Load an image
   * 
   * @param string $file
   * @throws Simplify_Thumb_ThumbException
   * @return resource
   */
  public static function load($file)
  {
    if (! file_exists($file) || ! is_file($file)) {
      throw new Simplify_Thumb_ThumbException('File not found');
    }

    $info = getimagesize($file);

    $originalType = $info[2];

    $image = null;

    switch ($originalType) {
      case IMAGETYPE_JPEG:
        $image = @imagecreatefromjpeg($file);
        break;

      case IMAGETYPE_GIF:
        $image = @imagecreatefromgif($file);
        break;

      case IMAGETYPE_PNG:
        $image = @imagecreatefrompng($file);
        break;
    }

    self::validateImageResource($image);

    return $image;
  }

  /**
   * Output an image
   * 
   * @param resource $image
   * @param string $type
   * @param int $quality
   * @param int $cacheSeconds
   */
  public static function output($image, $type = null, $quality = null, $cacheSeconds = 604800)
  {
    if (empty($type)) {
      $type = IMAGETYPE_JPEG;
    }

    self::validateImageResource($image);

    if ($cacheSeconds) {
      header("Cache-Control: private, max-age={$cacheSeconds}, pre-check={$cacheSeconds}");
      header("Expires: " . date(DATE_RFC822, strtotime("{$cacheSeconds} seconds")));
      header("Pragma: private");
    } else {
      header("Pragma: no-cache");
    }

    header('Content-Type: ' . self::getImageMimeType($type));

    switch ($type) {
      case IMAGETYPE_JPEG:
        imagejpeg($image, null, $quality);
        break;

      case IMAGETYPE_GIF:
        imagegif($image);
        break;

      case IMAGETYPE_PNG:
        imagepng($image);
        break;
    }

    exit();
  }

  /**
   * Save an image
   * 
   * @param resource $image
   * @param string $file
   * @param string $type
   * @param int $quality
   */
  public static function save($image, $file, $type = null, $quality = 99)
  {
    self::validateImageResource($image);

    if (empty($type)) {
      $type = IMAGETYPE_JPEG;
    }

    switch ($type) {
      case IMAGETYPE_JPEG:
        imagejpeg($image, $file, $quality);
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
   * Destroy image resource
   * 
   * @param resource $image
   */
  public static function destroy($image)
  {
    self::validateImageResource($image);
    imagedestroy($image);
  }

  /**
   * Validate image resource
   * 
   * @param resource $image
   * @throws Simplify_Thumb_ThumbException
   */
  public static function validateImageResource($image)
  {
    if ($image === null) {
      throw new Simplify_Thumb_ThumbException('No image specified');
    }

    if ($image === false) {
      throw new Simplify_Thumb_ThumbException('File not found or not a valid image file');
    }
  }

  /**
   * Get mime type
   * 
   * @param string $type
   * @return string
   */
  public static function getImageMimeType($type)
  {
    return image_type_to_mime_type($type);
  }

  /**
   * Get the font size the fits text inside $width and $height
   * 
   * @param int $width
   * @param int $height
   * @param string $font
   * @param string $text
   * @param int $minsize
   * @param int $maxsize
   * @param int $inc
   * @return int
   */
  public static function fitText($width = null, $height = null, $font = null, $text = null, $minsize = 0, $maxsize = 200, $inc = 2)
  {
    $size = $minsize;

    $tw = 0;
    $h = 0;

    while ($tw < $width && $size < $maxsize) {
      $size += $inc;

      $box = imagettfbbox($size, 0, $font, $text);

      $tw = abs($box[2]) + abs($box[0]);
      $th = abs($box[1]) + abs($box[5]);
    }

    if ($tw >= $width) $size -= $inc;

    return $size;
  }

  /**
   * Resize image
   * 
   * @param resource $image
   * @param int $width
   * @param int $height
   * @param bool $proportional
   * @param bool $fitInside
   * @param bool $far
   * @param int $background
   * @throws Simplify_Thumb_ThumbException
   * @return unknown|Simplify_Thumb_Functions|resource
   */
  public static function resize($image, $width = null, $height = null, $proportional = true, $fitInside = true, $far = false, $background = 0xffffff)
  {
    self::validateImageResource($image);

    if (empty($width) && empty($height)) return $image;

    $w0 = imagesx($image);
    $h0 = imagesy($image);

    $w1 = $w2 = empty($width) ? $w0 : $width;
    $h1 = $h2 = empty($height) ? $h0 : $height;

    if ($w0 == $w2 && $h0 == $h2) return $this;

    if ($proportional) {
      if ($fitInside) {
        if (($w0 / $h0) > ($w1 / $h1)) {
          $w2 = $w1;
          $prop = $w2 / $w0;
          $h2 = $h0 * $prop;
        } else {
          $h2 = $h1;
          $prop = $h2 / $h0;
          $w2 = $w0 * $prop;
        }
      } else {
        if (($w0 / $h0) > ($w1 / $h1)) {
          $h2 = $h1;
          $prop = $h2 / $h0;
          $w2 = $w0 * $prop;
        } else {
          $w2 = $w1;
          $prop = $w2 / $w0;
          $h2 = $h0 * $prop;
        }
      }
    }

    $w2 = floor($w2);
    $h2 = floor($h2);

    $x0 = 0;
    $y0 = 0;

    if ($far) {
      $temp = imagecreatetruecolor($width, $height);

      $x0 = ($width - $w2) / 2;
      $y0 = ($height - $h2) / 2;
    } else {
      $temp = imagecreatetruecolor($w2, $h2);
    }

    if ($background === false) {
      imagealphablending($temp, false);
      $trans = imagecolorallocatealpha($temp, 255, 255, 255, 127);
      imagefilledrectangle($temp, 0, 0, $width, $height, $trans);
      imagealphablending($temp, true);
      imagesavealpha($temp, true);
    } else {
      imagefilledrectangle($temp, 0, 0, $width, $height, $background);
    }

    imagealphablending($temp, true);
    imagesavealpha($temp, false);

    if (! imagecopyresampled($temp, $image, $x0, $y0, 0, 0, $w2, $h2, $w0, $h0)) {
      throw new Simplify_Thumb_ThumbException('There was an error resizing the image');
    }

    return $temp;
  }

  /**
   * Crop image
   * 
   * @param resource $image
   * @param int $x
   * @param int $y
   * @param int $width
   * @param int $height
   * @throws Simplify_Thumb_ThumbException
   * @return resource
   */
  public static function crop($image, $x, $y, $width, $height)
  {
    self::validateImageResource($image);

    $temp = imagecreatetruecolor($width, $height);

    if (! imagecopyresampled($temp, $image, 0, 0, $x, $y, $width, $height, $width, $height)) {
      throw new Simplify_Thumb_ThumbException('There was an error cropping the image');
    }

    return $temp;
  }

}
