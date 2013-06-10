<?php

if (! defined('SY_DIR')) {
  define('SY_DIR', dirname(__file__));

  require_once('functions.php');

  require_once ('Simplify' . DIRECTORY_SEPARATOR . 'Autoload.php');

  sy_autoload_register(array('Simplify_Autoload', 'autoload'));
}

require_once ('Simplify' . DIRECTORY_SEPARATOR . 'Simplify.php');

Simplify_Autoload::registerPath(SY_DIR);
Simplify_Autoload::registerPath(SY_DIR . DIRECTORY_SEPARATOR . 'vendor');
