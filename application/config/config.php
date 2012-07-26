<?php
$config['session_name'] = 'control-gastos';
$config['debug'] = true;
$db_config['default']['host'] = 'sahib-mysql-server';
$db_config['default']['database'] = 'expenditure_control';
$db_config['default']['user'] = 'root';
$db_config['default']['password'] = 'rootsql';
$db_config['default']['options'] = array();
$db_config['default']['charset'] = 'UTF8';
$routes = array(
  // Recuperar password
  'recover-password' => 'home/recover-password',
  // Salir
  'salir' => 'home/logout'
);
