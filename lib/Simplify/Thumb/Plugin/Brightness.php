<?php

class Simplify_Thumb_Plugin_Brightness extends Simplify_Thumb_Plugin
{

  protected function process(Simplify_Thumb_Processor $thumb, $level)
  {
    Simplify_Thumb_Functions::validateImageResource($thumb->image);
    imagefilter($thumb->image, IMG_FILTER_BRIGHTNESS, $level);
  }

}
