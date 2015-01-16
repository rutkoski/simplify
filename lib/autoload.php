<?php

error_reporting(E_ALL);

ini_set('display_errors', 1);

require_once('Simplify.php');

//class_alias('Simplify', 's');
//class_alias('Simplify', 'Simplify\s');

require_once('debug.php');
require_once('l10n.php');

use Simplify as s;

$config = Simplify::config();

$config['app_url'] = Simplify::request()->url();

$config['cache_dir'] = '{app_dir}/cache/';

$config['templates:path:'] = '{app_dir}/templates/{theme}/';
$config['templates:path:'] = '{app_dir}/templates/';

$config['modules_dir'] = '{app_dir}/modules/';

$config['public_path'] = '/';

$config['www_dir'] = dirname($_SERVER['SCRIPT_FILENAME']) . '/{public_path}/';
$config['www_url'] = '{app_url}/{public_path}/';

$config['files_path'] = 'files/';
$config['files_dir'] = '{www_dir}/{files_path}/';
$config['files_url'] = '{www_url}/{files_path}/';

$config['theme'] = 'default';
$config['theme_dir'] = '{www_dir}/{theme}/';
$config['theme_url'] = '{www_url}/{theme}/';

/*if (file_exists(APP_DIR . '/config/config.php')) {
  require_once(APP_DIR . '/config/config.php');
}*/
