<?php
header('Content-type: text/html; charset=utf-8');

require_once(__DIR__.'/../vendor/autoload.php');
require_once(__DIR__.'/../app/config.php');

use Respect\Rest\Router;
use Core\Controle\Admin;
use Core\Controle\Ataque;
use Core\Controle\Operacao;
use Core\Controle\Usuario;
use Core\Controle\Relatorio;
use Lib\Visao;

session_start();

$r3 = new Router('/brutus');

$r3->isAutoDispatched = false;

$r3->any(array(
		'/',
		'/ataque'
), new Ataque);

$r3->get(array(
		'/',
		'/ataque'
), new Admin);

$r3->get('/ataque/cadastro/*', [new Ataque, 'cadastro']);
$r3->get('/ataque/usuario/*', [new Ataque, 'usuario']);

$r3->any('/operacao/*', new Operacao);

$r3->get(array(
		'/operacao/cadastro/*',
		'/operacao/cadastro'
), [new Operacao, 'cadastro']);

$r3->get('/operacao/grafico', [new Operacao, 'grafico']);

$r3->any('/usuario/', new Usuario);
$r3->get('/usuario/cadastro/*', [new Usuario, 'cadastro']);

$r3->get('/relatorio/usuario/', [new Relatorio, 'usuario']);
$r3->get('/relatorio/ataque/', [new Relatorio, 'ataque']);

$r3->any('/admin', new Admin);
$r3->get('/admin/logout', [new Admin, 'logout']);

$r3->run();

if (!isset($r3->request->route)) {
	$v = new Visao;
	
	try {
		$v->setTemplate('404');
		$v->render(array(
				'dirPublic' => DIR_PUBLIC,
				'dirRoot' => DIR_ROOT,
				'classMenuAtivo' => null
		));
	} catch (Exception $e) {
		die('ERRO TWIG: ' . $e->getMessage());
	}
}
