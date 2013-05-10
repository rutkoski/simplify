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
    if (!file_exists($file) || !is_file($file)) {
      throw new Simplify_ThumbException('File not found');
    }

    $info = getimagesize($file);

    $originalType = $info[2];

    $image = null;

    switch ($originalType) {
      case IMAGETYPE_JPEG :
        $image = @imagecreatefromjpeg($file);
        break;

      case IMAGETYPE_GIF :
        $image = @imagecreatefromgif($file);
        break;

      case IMAGETYPE_PNG :
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
    }
    else {
      header("Pragma: no-cache");
    }

    header('Content-Type: ' . self::getImageMimeType($type));

    switch ($type) {
      case IMAGETYPE_JPEG :
        imagejpeg($image, null, $quality);
        break;

      case IMAGETYPE_GIF :
        imagegif($image);
        break;

      case IMAGETYPE_PNG :
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
      case IMAGETYPE_JPEG :
        imagejpeg($image, $file, $quality);
        break;

      case IMAGETYPE_GIF :
        imagesavealpha($image, true);
        imagegif($image, $file);
        break;

      case IMAGETYPE_PNG :
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
      throw new Simplify_ThumbException('No image specified');
    }

    if ($image === false) {
      throw new Simplify_ThumbException('File not found or not a valid image file');
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

    if ($tw >= $width)
      $size -= $inc;

    return $size;
  }

  /**
   * Calculate size to fit image inside $width and $height
   *
   * @param int $w0 original width
   * @param int $h0 original height
   * @param int $width reference width
   * @param int $height reference height
   * @return array 0 => final width, 1 => final height, 'w' => final width, 'h' => final height
   */
  public static function fitInside($w0, $h0, $width, $height)
  {
    if (($w0 / $h0) > ($width / $height)) {
      $w1 = $width;
      $prop = $w1 / $w0;
      $h1 = $h0 * $prop;
    }
    else {
      $h1 = $height;
      $prop = $h1 / $h0;
      $w1 = $w0 * $prop;
    }

    return array($w1, $h1, 'w' => $w1, 'h' => $h1);
  }

  /**
   * Calculate size to fit image outside $width and $height
   *
   * @param int $w0 original width
   * @param int $h0 original height
   * @param int $width reference width
   * @param int $height reference height
   * @return array 0 => final width, 1 => final height, 'w' => final width, 'h' => final height
   */
  public static function fitOutside($w0, $h0, $width, $height)
  {
    if (($w0 / $h0) > ($width / $height)) {
      $h1 = $height;
      $prop = $h1 / $h0;
      $w1 = $w0 * $prop;
    }
    else {
      $w1 = $width;
      $prop = $w1 / $w0;
      $h1 = $h0 * $prop;
    }

    return array($w1, $h1, 'w' => $w1, 'h' => $h1);
  }

  /**
   * Resize image
   *
   * @param resource $image
   * @param int $width
   * @param int $height
   * @param int $mode
   * @param bool $far
   * @param int $background
   * @throws Simplify_Thumb_ThumbException
   * @return resource
   */
  public static function resize($image, $width = null, $height = null, $mode = Simplify_Thumb::FIT_INSIDE, $far = false, $r = 0, $g = 0, $b = 0, $a = 0)
  {
    self::validateImageResource($image);

    if (empty($width) && empty($height))
      return $image;

    $w0 = imagesx($image);
    $h0 = imagesy($image);

    if ($w0 == $width && $h0 == $height)
      return $image;

    switch ($mode) {
      case Simplify_Thumb::FIT_INSIDE :
        $size = self::fitInside($w0, $h0, $width, $height);
        $w1 = $size[0];
        $h1 = $size[1];
        $w2 = $far ? $width : $w1;
        $h2 = $far ? $height : $h1;
        break;

      case Simplify_Thumb::FIT_OUTSIDE :
        $size = self::fitOutside($w0, $h0, $width, $height);
        $w1 = $size[0];
        $h1 = $size[1];
        $w2 = $far ? $width : $w1;
        $h2 = $far ? $height : $h1;
        break;

      case Simplify_Thumb::NO_SCALE :
        $w1 = $w0;
        $h1 = $h0;
        $w2 = $far ? $width : $w1;
        $h2 = $far ? $height : $h1;
        break;

      case Simplify_Thumb::SCALE_TO_FIT :
        $w1 = $w2 = $width;
        $h1 = $h2 = $height;
        break;
    }

    $x0 = ($w2 - $w1) / 2;
    $y0 = ($h2 - $h1) / 2;

    $temp = self::createTransparentImage($w2, $h2, $r, $g, $b, $a);

    if (!imagecopyresampled($temp, $image, $x0, $y0, 0, 0, $w1, $h1, $w0, $h0)) {
      throw new Simplify_ThumbException('There was an error resizing the image');
    }

    return $temp;
  }

  /**
   * Create and empty transparent image
   *
   * @param int $w final width
   * @param int $h final height
   * @return resource image
   */
  public static function createTransparentImage($w, $h, $r = 255, $g = 255, $b = 255, $a = 127)
  {
    $temp = imagecreatetruecolor($w, $h);
    imagesavealpha($temp, true);
    imagealphablending($temp, false);
    $trans = imagecolorallocatealpha($temp, $r, $g, $b, $a);
    imagefill($temp, 0, 0, $trans);
    imagealphablending($temp, true);
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
  public static function crop($image, $x, $y, $width, $height, $r = 0, $g = 0, $b = 0, $a = 0)
  {
    self::validateImageResource($image);

    $temp = self::createTransparentImage($width, $height, $r, $g, $b, $a);

    $w = imagesx($image);
    $h = imagesy($image);

    imagecopy($temp, $image, $x > 0 ? 0 : -$x, $y > 0 ? 0 : -$y, $x > 0 ? $x : 0, $y > 0 ? $y : 0,
      $w - ($x > 0 ? $x : 0), $h - ($y > 0 ? $y : 0));

    return $temp;
  }

  /**
   * Offset image size
   *
   * @param resource $image
   * @param int $top offset top
   * @param int $right offset left
   * @param int $bottom offset bottom
   * @param int $left offset left
   * @param int $r background red channel (0 - 255)
   * @param int $g background green channel (0 - 255)
   * @param int $b background blue channel (0 - 255)
   * @param int $a background alpha channel (0 = opaque, 127 = transparent)
   * @return Simplify_Thumb
   */
  public static function offset($image, $top, $right, $bottom, $left, $r = 0, $g = 0, $b = 0, $a = 0)
  {
    $w = imagesx($image);
    $h = imagesy($image);

    $temp = Simplify_Thumb_Functions::createTransparentImage($w + $left + $right, $h + $top + $bottom, $r, $g, $b, $a);

    imagecopy($temp, $image, $left > 0 ? $left : 0, $top > 0 ? $top : 0, $left > 0 ? 0 : -$left, $top > 0 ? 0 : -$top,
      $w, $h + min($top, 0));

    return $temp;
  }

  /**
   * Zoom crop
   *
   * @param resource $image
   * @param int $width
   * @param int $height
   * @param int $gravity
   * @return resource
   */
  public static function zoomCrop($image, $width, $height, $gravity = Simplify_Thumb::CENTER)
  {
    $image = Simplify_Thumb_Functions::resize($image, $width, $height, Simplify_Thumb::FIT_OUTSIDE);

    $w0 = imagesx($image);
    $h0 = imagesy($image);

    $w1 = empty($width) ? $w0 : $width;
    $h1 = empty($height) ? $h0 : $height;

    if ($w0 == $w1 && $h0 == $h1)
      return;

    switch ($gravity) {
      case Simplify_Thumb::TOP_LEFT :
      case Simplify_Thumb::LEFT :
      case Simplify_Thumb::BOTTOM_LEFT :
        $x = 0;
        break;
      case Simplify_Thumb::TOP_RIGHT :
      case Simplify_Thumb::RIGHT :
      case Simplify_Thumb::BOTTOM_RIGHT :
        $x = $w0 - $w1;
        break;
      case Simplify_Thumb::CENTER :
      default :
        $x = floor($w0 - $w1) / 2;
    }

    switch ($gravity) {
      case Simplify_Thumb::TOP_LEFT :
      case Simplify_Thumb::TOP :
      case Simplify_Thumb::TOP_RIGHT :
        $y = 0;
        break;
      case Simplify_Thumb::BOTTOM_LEFT :
      case Simplify_Thumb::BOTTOM :
      case Simplify_Thumb::BOTTOM_RIGHT :
        $y = $h0 - $h1;
        break;
      case Simplify_Thumb::CENTER :
      default :
        $y = floor($h0 - $h1) / 2;
    }

    return Simplify_Thumb_Functions::crop($image, $x, $y, $w1, $h1);
  }

}
