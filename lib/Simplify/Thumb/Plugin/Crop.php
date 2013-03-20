<?php

class Simplify_Thumb_Plugin_Crop extends Simplify_Thumb_Plugin
{

  protected function process(Simplify_Thumb_Processor $thumb, $x, $y, $width, $height)
  {
    $thumb->image = Simplify_Thumb_Functions::crop($thumb->image, $x, $y, $width, $height);
  }

}
