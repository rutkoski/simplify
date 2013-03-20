<?php

class Simplify_Thumb_Functions
{

  /**
   *
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
   *
   * @return void
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
   *
   * @return void
   */
  public static function destroy($image)
  {
    self::validateImageResource($image);
    imagedestroy($image);
  }

  public static function validateImageResource($image)
  {
    if ($image === null) {
      throw new Simplify_Thumb_ThumbException('No image specified');
    }

    if ($image === false) {
      throw new Simplify_Thumb_ThumbException('File not found or not a valid image file');
    }
  }

  public static function getImageMimeType($type)
  {
    return image_type_to_mime_type($type);
  }

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
   *
   * @return void
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
