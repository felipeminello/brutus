<?php
header('Content-type: text/html; charset=utf-8');

require_once(__DIR__.'/../vendor/autoload.php');
require_once(__DIR__.'/../app/config.php');

use Lib\AltoRouter;


use PHPRouter\RouteCollection;
use PHPRouter\Router;
use PHPRouter\Route;

$collection = new RouteCollection();

$collection->attachRoute(new Route('/relatorio/', array(
		'_controller' => 'Relatorio::usuario',
		'methods' => 'GET'
)));

$collection->attachRoute(new Route('/', array(
		'_controller' => 'someController::indexAction',
		'methods' => 'GET'
)));
/*
$router = new Router($collection);
$router->setBasePath('/brutus');
$route = $router->matchCurrentRequest();

var_dump($route);
