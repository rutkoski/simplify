<?php
/**
 * SimplifyPHP Framework
 *
 * This file is part of SimplifyPHP Framework.
 *
 * SimplifyPHP Framework is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * SimplifyPHP Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Rodrigo Rutkoski Rodrigues, <rutkoski@gmail.com>
 * @copyright Copyright 2008 Rodrigo Rutkoski Rodrigues
 */

global $php_errno;

$php_errno = array(
  0 => 'E_NONE', 1 => 'E_ERROR', 2 => 'E_WARNING', 4 => 'E_PARSE', 8 => 'E_NOTICE', 16 => 'E_CORE_ERROR', 32 => 'E_CORE_WARNING', 64 => 'E_COMPILE_ERROR', 128 => 'E_COMPILE_WARNING',
  256 => 'E_USER_ERROR', 512 => 'E_USER_WARNING', 1024 => 'E_USER_NOTICE', 2048 => 'E_STRICT', 4096 => 'E_RECOVERABLE_ERROR', 8192 => 'E_DEPRECATED', 16384 => 'E_USER_DEPRECATED',
  30719 => 'E_ALL'
);

foreach ($php_errno as $no => $err) {
  sy_define_once($err, $no);
}

define('SY_DEBUG_NONE', 0);
define('SY_DEBUG_WARNINGS', 1);
define('SY_DEBUG_ERRORS', 2);
define('SY_DEBUG_ALL', 3);

sy_debug_level(SY_DEBUG_ALL);

set_error_handler('sy_exception_error_handler');
set_exception_handler('sy_exception_handler');

function sy_debug_level($level = null)
{
  static $_level;

  if (! is_null($level)) {
    $_level = $level;

    switch ($level) {
      case SY_DEBUG_NONE :
        error_reporting(E_NONE & ~E_STRICT);
        ini_set('display_errors', 0);
        break;

      case SY_DEBUG_WARNINGS :
        error_reporting(E_ALL & ~(E_DEPRECATED | E_STRICT));
        ini_set('display_errors', 1);
        break;

      case SY_DEBUG_ERRORS :
        error_reporting(E_ALL & ~(E_NOTICE | E_DEPRECATED | E_STRICT));
        ini_set('display_errors', 1);
        break;

      default :
        error_reporting(E_ALL & ~E_STRICT);
        ini_set('display_errors', 1);
    }
  }

  return $_level;
}

function sy_log($file, $data, $lines = 300)
{
  static $logs;

  if (empty($logs)) {
    $logs = array();
  }

  if (! Simplify::config()) {
    return;
  }

  $file = APP_DIR . '/logs/' . $file . '.log';

  $a = @file($file, FILE_IGNORE_NEW_LINES);
  if ($lines && count($a) > $lines) {
    $a = array_slice($a, count($a) - $lines);
  }

  if (! isset($logs[$file])) {
    $logs[$file] = true;
    $a[] = "-> started " . date('Y-m-d H:i:s');
  }

  $output = date('Y-m-d H:i:s') . ' -> ';

  if (! empty($data)) {
    if (is_string($data)) {
      $output .= trim($data);
    }
    elseif ($data instanceof \Simplify\DictionaryInterface) {
      $data = $data->getAll();
      array_walk_recursive($data, 'sy_array_map');
      $output .= var_export($data, true);
    }
    else {
      $output .= var_export($data, true);
    }
  }

  $a[] = $output;

  file_put_contents($file, implode("\n", $a));
}

function sy_exception_error_handler($errno, $errstr, $errfile, $errline)
{
  if ((error_reporting() & $errno) == $errno) {
    return sy_exception_handler(new ErrorException($errstr, 0, $errno, $errfile, $errline));
  }

  return false;
}

function sy_exception_handler($e)
{
  if (sy_debug_level() && Simplify::request()->json()) {
    header('Content-type: application/json; charset="utf-8"');
    $response = array('exception' => array('class' => get_class($e), 'message' => $e->getMessage(), 'trace' => $e->getTraceAsString(), 'file' => $e->getFile(), 'line' => $e->getLine()));
    $output = json_encode($response);
  }
  else {
    $output = '';
    $output .= '<p>' . nl2br($e->getMessage()) . '</p><p>';
    $output .= '<pre>' . htmlentities($e->getTraceAsString()) . '</pre>';
    $output .= '</p>';
    $output .= '<p>' . get_class($e) . ' thrown in ' . $e->getFile() . ' at line ' . $e->getLine() . '</p>';
    $output .= '';
  }

  ob_clean();
  echo $output;
  exit();
}
