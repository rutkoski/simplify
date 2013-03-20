<?php

class Simplify_Thumb_Plugin_ZoomCrop extends Simplify_Thumb_Plugin
{

  protected function process(Simplify_Thumb_Processor $thumb, $width = null, $height = null, $gravity = Thumb::CENTER)
  {
    $image = $thumb->image;

    $temp = Simplify_Thumb_Functions::resize($image, $width, $height, true, false);

    $w0 = imagesx($temp);
    $h0 = imagesy($temp);

    $w1 = empty($width) ? $w0 : $width;
    $h1 = empty($height) ? $h0 : $height;

    if ($w0 == $w1 && $h0 == $h1) return $this;

    switch ($gravity) {
      case Thumb::TOP_LEFT: case Thumb::LEFT: case Thumb::BOTTOM_LEFT: $x = 0; break;
      case Thumb::TOP_RIGHT: case Thumb::RIGHT: case Thumb::BOTTOM_RIGHT: $x = $w0 - $w1; break;
      case Thumb::CENTER: default: $x = floor($w0 - $w1) / 2;
    }

    switch ($gravity) {
      case Thumb::TOP_LEFT: case Thumb::TOP: case Thumb::TOP_RIGHT: $y = 0; break;
      case Thumb::BOTTOM_LEFT: case Thumb::BOTTOM: case Thumb::BOTTOM_RIGHT: $y = $h0 - $h1; break;
      case Thumb::CENTER: default: $y = floor($h0 - $h1) / 2;
    }

    $temp = Simplify_Thumb_Functions::crop($temp, $x, $y, $w1, $h1);

    $thumb->image = $temp;
  }

}
