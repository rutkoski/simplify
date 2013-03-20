<?php

class Simplify_Thumb_Plugin_Resize extends Simplify_Thumb_Plugin
{

  protected function process(Simplify_Thumb_Processor $thumb, $width = null, $height = null, $proportional = true, $fitInside = true, $far = false, $background = 0xffffff)
  {
    $thumb->image = Simplify_Thumb_Functions::resize($thumb->image, $width, $height, $proportional, $fitInside, $far, $background);
  }

}
