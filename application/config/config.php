<?php

// ENVIRONMENT CONFIG

$config['session_name'] = 'gassto-web-app';
$config['debug'] = true;

// DATABASE CONFIG

$db_config['default']['host'] = 'sahib-mysql-server';
$db_config['default']['database'] = 'gassto';
$db_config['default']['user'] = 'root';
$db_config['default']['password'] = 'rootsql';
$db_config['default']['options'] = array();
$db_config['default']['charset'] = 'UTF8';

// ROUTES

$routes = array(
  // Recover password
  'recover-password' => 'home/recover-password',
  // Logout
  'salir' => 'home/logout'
);
