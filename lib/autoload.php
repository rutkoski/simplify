<?php

define('SY_DIR', dirname(__file__));

require_once('functions.php');

require_once ('Simplify' . DIRECTORY_SEPARATOR . 'Simplify.php');
require_once ('Simplify' . DIRECTORY_SEPARATOR . 'Autoload.php');

Simplify_Autoload::registerPath(SY_DIR);
Simplify_Autoload::registerPath(SY_DIR . DIRECTORY_SEPARATOR . 'vendor');

sy_autoload_register(array('Simplify_Autoload', 'autoload'));
