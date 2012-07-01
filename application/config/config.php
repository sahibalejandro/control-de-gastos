<?php
/**
 * Nombre de la sesión, para evitar sobreescribir sesiones de otros sitios
 * basados en quark
 */
$config['session_name'] = 'control-de-gastos';

/**
 * Tiempo de vida (en segundos) de la cookie, por default 15 días.
 */
$config['cookie_life_time'] = 1296000;

/**
 * Dominio de la cookie, cuando se van a utilizar sitios en multiples lenguajes y
 * el prefijo de lenguaje se encuentra en el subdominio, es necesario especificar
 * aquí el nombre del dominio al cual pertenecerá la cookie, para que este
 * disponible en todos los subdominios, si no se utiliza especificar null
 */
$config['cookie_domain'] = null;

/**
 * Si de debug mode es true, se mostraran los detalles de los errores en los
 * mensajes de error.
 * Es responsabilidad del usuario mostrar los mensajes en las vistas
 * personalizadas.
 */
$config['debug'] = true;

/**
 * Lista de prefijos de lenguaje que se utilizaran para un sitio multi lenguaje,
 * el primer lenguaje definido será el lenguaje por default.
 */
$config['langs'] = array();

/**
 * Si lang on subdomain es true, el prefijo del lenguaje se tomará del subdominio
 * y no del path info, esto tambien afectara el comportamiento de los metodos de
 * QuarkURL para generar las URL validas con el prefijo de lenguaje en el
 * subdominio.
 */
$config['lang_on_subdomain'] = false;

/**
 * Flags para error_reporting()
 */
$config['error_reporting'] = E_ALL ^ E_DEPRECATED;

/**
 * Lista de archivos a incluir antes de instanciar el controlador, por lo general
 * estos archivos son para definir constantes y/o funciones para que esten
 * disponibles desde cualquier punto en la aplicación.
 * Estos archivos deberan estar alojados en el directorio "application/includes"
 */
$config['auto_includes'] = array();

/**
 * Lista de directorios (a partir del root de la aplicación) donde __autoload()
 * buscará los archivos de definición de clases, es útil por ejemplo si quieremos
 * organizar controladores en subdirectorios dentro del diectorio
 * "application/controllers"
 */
$config['class_paths'] = array();

/*
 * Host de la base de datos
 */
$db_config['default']['host'] = 'sahib-mysql-server';
/*
 * Nombre de la base de datos
 */
$db_config['default']['database'] = 'control_gastos';
/*
 * Usuario para conectar a la base de datos
 */
$db_config['default']['user'] = 'root';
/*
 * Password el usuario
 */
$db_config['default']['password'] = 'rootsql';
/*
 * Opciones para pasar al constructor de PDO
 */
$db_config['default']['options'] = array();
/*
 * Codificación de caracteres para utilizar en las consultas (Ej. SET NAMES "UTF8")
 */
$db_config['default']['charset'] = 'UTF8';

/*
 * Lista de rutas, array asociativo, el key es la expresión a evaluar y el valor
 * es la ruta que será reemplazada.
 * 
 * Ej. "home/([0-9]+)" => 'home/entry/$1'
 * Explicación:
 * Toda url que coincida con "home/<cualquier-numero>" sera redireccionada a
 * "home/entry/<numero>"
 */
$routes = array();
