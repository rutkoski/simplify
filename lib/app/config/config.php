<?php

global $config;

sy_debug_level(SY_DEBUG_NONE);

$config['database'] = array(
  'default' => array(
    // global
    '*' => array(
      'host' => 'localhost',
      'username' => 'root',
      'password' => 'plan',
      'name' => 'amplify',
      'charset' => 'utf8'
    ),

    // production
    'www.domain.com.br' => array(
      'username' => 'root',
      'password' => 'plan',
      'name' => 'amplify'
     )
   )
);

require_once('routes.php');
