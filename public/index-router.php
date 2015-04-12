<?php
header('Content-type: text/html; charset=utf-8');

require_once(__DIR__.'/../vendor/autoload.php');
require_once(__DIR__.'/../app/config.php');

use Lib\Router;

$leo_routes = array(
		'brutus' => array( // Default controller
				'controller' => 'Admin',
				'method' => 'login',
				'number' => 'id',
				'param' => 'opt_param'
		),
		'brutus/relatorio' => array(
				'controller' => 'Relatorio',
				'method' => 'usuario',
				'number' => 'id',
				'param' => 'opt_param'
		),
		'admin/page' => array(
				'controller' => 'admin',
				'method' => 'page',
				'number' => 'id',
				'param' => 'opt_param'
		),
		'admin/authService' => array(
				'controller' => 'admin',
				'method' => 'authService',
				'number' => 'id',
				'param' => 'opt_param'
		),
		'admin' => array(
				'controller' => 'admin',
				'method' => 'index',
				'number' => 'id',
				'param' => 'opt_param'
		),
		'welcome/index' => array(
				'controller' => 'welcome',
				'method' => 'index',
				'number' => 'id',
				'param' => 'opt_param'
		),
		'error404' => array(
				'controller' => 'errors',
				'method' => 'error404'
		)
);

Router::start($leo_routes);