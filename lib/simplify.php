<?php

error_reporting(E_ALL);

ini_set('display_errors', 1);

require_once('autoload.php');
require_once('debug.php');
require_once('l10n.php');

Simplify_Autoload::registerPath(APP_DIR);

$config = s::config();

$config['app_dir'] = APP_DIR;
$config['app_url'] = s::request()->url();

$config['cache_dir'] = '{app_dir}/cache';

$config['templates_dir'] = '{app_dir}/templates/{theme}';

//$config['modules:dir'] = '{app_dir}/../vendor';
$config['modules:app:path'] = '{app_dir}';

$config['public_path'] = '';

$config['www_dir'] = dirname($_SERVER['SCRIPT_FILENAME']) . '{public_path}';
$config['www_url'] = '{app_url}{public_path}';

$config['files_path'] = '/files';
$config['files_dir'] = '{www_dir}{files_path}';
$config['files_url'] = '{www_url}{files_path}';

$config['theme'] = 'default';
$config['theme_dir'] = '{www_dir}/{theme}';
$config['theme_url'] = '{www_url}/{theme}';

$config['view:helpers:html'] = 'Simplify_View_Helper_Html';
$config['view:helpers:cycle'] = 'Simplify_View_Helper_Cycle';
$config['view:helpers:form'] = 'Simplify_View_Helper_Form';
$config['view:helpers:pager'] = 'Simplify_View_Helper_Pager';
$config['view:helpers:string'] = 'Simplify_View_Helper_String';

if (file_exists(APP_DIR . '/config/config.php')) {
  require_once(APP_DIR . '/config/config.php');
}
