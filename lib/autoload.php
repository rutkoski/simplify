<?php

error_reporting(E_ALL);

ini_set('display_errors', 1);
//ini_set('session.cookie_httponly', 1);
//ini_set('session.cookie_secure boolean', 1);

if (!defined('SY_APP_DIR')) {
  trigger_error('SY_APP_DIR not defined', E_USER_ERROR);
}

require_once('Simplify.php');
require_once('debug.php');
require_once('l10n.php');

if (! interface_exists('JsonSerializable')) {
  require_once('JsonSerializable.php');
}

define('SY_DIR', preg_replace('#[\\\/]+#', '/', __dir__ . '/'));

$config = Simplify::config();

$config['sy:dir'] = SY_DIR;

$config['app:path'] = 'app/';
$config['app:dir'] = SY_APP_DIR; // absolute, trailing slash
$config['app:url'] = Simplify::request()->url(); // absolute, no trailing slash

$config['view:default'] = '\Simplify\View\Twig';

$config['cache:dir'] = '{www:dir}cache/';
$config['cache:url'] = '{www:url}cache/';

$config['app:assets:path:'] = '{app:path}assets/{theme}/';
$config['app:assets:path:'] = '{app:path}assets/';

$config['templates:path:'] = '{app:dir}templates/{theme}/';
$config['templates:path:'] = '{app:dir}templates/';
$config['templates:path:'] = '{www:dir}dist/app/templates/{theme}';

$config['public_path'] = '/';

$config['www:dir'] = dirname($_SERVER['SCRIPT_FILENAME']) . '{public_path}';
$config['www:url'] = '{app:url}{public_path}';

$config['files:path'] = 'files/';
$config['files:dir'] = '{www:dir}{files:path}';
$config['files:url'] = '{www:url}{files:path}';

$config['theme'] = 'default';

if (file_exists($config['app:dir'] . 'config/config.php')) {
  require_once($config['app:dir'] . 'config/config.php');
}
