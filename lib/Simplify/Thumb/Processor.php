<?php

class Simplify_Thumb_Processor
{

  public $quality = 99;

  protected $image;

  public function callPlugin($name)
  {
    $params = func_get_args();

    array_shift($params);

    $class = $name;

    $plugin = new $class;

    call_user_func_array(array($plugin, 'process'), array_merge(array($this), $params));
  }

  public function quality($q)
  {
    $this->quality = $q;
  }

  public function load($file)
  {
    if (! file_exists($file) || ! is_file($file)) {
      throw new ThumbException('File not found');
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
   *
   * @return void
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

  public function getImageMimeType($type = null)
  {
    if (empty($type)) {
      $type = $this->originalType;
    }
    return image_type_to_mime_type($type);
  }

}
