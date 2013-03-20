<?php

if (! defined('APP_DIR')) {
  define('APP_DIR', realpath(dirname(__file__)));
  require_once(APP_DIR . DIRECTORY_SEPARATOR . 'simplify.php');
}

s::app()->dispatch();
