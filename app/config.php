<?php
if ($_SERVER['SERVER_NAME'] == '192.168.1.4') {
	error_reporting(E_ALL);
//	error_reporting(E_ERROR | E_WARNING | E_PARSE);

	define('DIR_PUBLIC', '/brutus/public/');
	define('DIR_ROOT', '/brutus/');


	define('DB_TIPO', 'mysql');
	define('DB_LOCAL', 'localhost');
	define('DB_USER', 'root');
	define('DB_PASSW', 'camarao');
	define('DB_DATABASE', 'boombeach');

	define('HTTP_HOST', 'http://'.$_SERVER['HTTP_HOST'].'/brutus/');
	define('HTTPS_HOST', 'https://'.$_SERVER['HTTP_HOST'].'/brutus/');

	define('EMAIL_DEBUG', 3);
	
	define('PASSWORD_COST', 9);
} else {
	error_reporting(0);
	ini_set('display_errors', 0);
	

	define('EMAIL_DEBUG', 0);
	
	define('PASSWORD_COST', 12);
}

define('DS', DIRECTORY_SEPARATOR);

define('DIR_FIS_ROOT', $_SERVER['DOCUMENT_ROOT'].DS.'brutus'.DS);
define('DIR_FIS_PUBLIC', DIR_FIS_ROOT.'public'.DS);

define('DIR_FIS_CACHE', DIR_FIS_ROOT.'app'.DS.'cache'.DS);

define('DIR_FIS_CONTROLE', DIR_FIS_ROOT.'src'.DS.'Core'.DS.'Controle'.DS);
define('DIR_FIS_MODELO', DIR_FIS_ROOT.'src'.DS.'Core'.DS.'Modelo'.DS);
define('DIR_FIS_SESSAO', DIR_FIS_ROOT.'app'.DS.'sessao'.DS);
define('DIR_FIS_VISAO', DIR_FIS_ROOT.'src'.DS.'Core'.DS.'Visao'.DS);

ini_set('session.save_handler', 'files');
session_save_path(DIR_FIS_SESSAO);
