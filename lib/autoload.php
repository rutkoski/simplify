<?php

if (! function_exists('sy_autoload_register')) {
  require_once('functions.php');
}

if (! class_exists('Simplify_Autoload')) {
  require_once ('Simplify' . DIRECTORY_SEPARATOR . 'Autoload.php');

  sy_autoload_register(array('Simplify_Autoload', 'autoload'));
}

if (! defined('SY_DIR')) {
  require_once ('Simplify/Simplify.php');

  define('SY_DIR', dirname(__file__));

  Simplify_Autoload::registerPath(SY_DIR);
  Simplify_Autoload::registerPath(SY_DIR . '/vendor');
}
