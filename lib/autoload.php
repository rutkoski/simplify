<?php

error_reporting(E_ALL);

ini_set('display_errors', 1);

if (!defined('SY_APP_DIR')) {
  trigger_error('SY_APP_DIR not defined', E_USER_ERROR);
}

require_once('Simplify.php');
require_once('debug.php');
require_once('l10n.php');

if (! interface_exists('JsonSerializable')) {
  require_once('JsonSerializable.php');
}

$config = Simplify::config();

$config['app_dir'] = SY_APP_DIR; // absolute, trailing slash
$config['app_url'] = Simplify::request()->url(); // absolute, no trailing slash

$config['cache_dir'] = '{app_dir}cache/';

$config['templates:path:'] = '{app_dir}templates/{theme_path}';
$config['templates:path:'] = '{app_dir}templates/';
$config['templates:path:'] = '{www_dir}dist/app/templates/{theme}';

$config['modules_dir'] = '{app_dir}modules/';

$config['public_path'] = '/';

$config['www_dir'] = dirname($_SERVER['SCRIPT_FILENAME']) . '{public_path}';
$config['www_url'] = '{app_url}{public_path}';

$config['files_path'] = 'files/';
$config['files_dir'] = '{www_dir}{files_path}';
$config['files_url'] = '{www_url}{files_path}';

$config['theme'] = 'default';
$config['theme_path'] = '{theme}/';
$config['theme_dir'] = '{www_dir}{theme_path}';

$config['theme_url'] = function($config) {
  $dir = $config['www_dir'] . 'dist/';
  return is_dir($dir) ? '{www_url}dist/{theme_path}' : '{www_url}{theme_path}';
};

$config['css_url'] = '{theme_url}stylesheets/';
$config['js_url'] = '{theme_url}scripts/';

if (file_exists($config['app_dir'] . 'config/config.php')) {
  require_once($config['app_dir'] . 'config/config.php');
}
