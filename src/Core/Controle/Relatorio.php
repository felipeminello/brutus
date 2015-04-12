<?php
namespace Core\Controle;

use Respect\Rest\Routable;
use Exception;
use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\NestedValidationExceptionInterface;

use Core\Modelo\Dados\Usuario as UsuarioDados;
use Core\Modelo\Dados\Ataque as AtaqueDados;
use Core\Modelo\Usuario as u;

class Relatorio implements Routable {
	private $Visao;
	
	public function __construct() {
		$this->Visao = new \Lib\Visao;
	}
	
	public static function usuario() {
		$uDados = new UsuarioDados;
		$aDados = new AtaqueDados;
		
		$dadosPagina = $array = $ataque = array();
		
		$persmissao['alterar'] = true;
		$persmissao['excluir'] = true;
		$persmissao['inserir'] = true;
		
		try {
			$arrayUsuario = $uDados->listar();
			$arrayAtaque = $aDados->listarPorUsuario();
			
			foreach ($arrayUsuario as $idUsuario => $u) {
				$ataque = (isset($arrayAtaque[$idUsuario])) ? $arrayAtaque[$idUsuario] : array('quantidade' => 0);
				
				$array[] = array('id' => $u->__get('id'), 'nome' => $u->__get('nome'), 'tipo' => $u->getTipo(), 'dataEntrada' => $u->__get('dataEntrada'), 'quantidade' => $ataque['quantidade']);
			}
			/*
			try {
				$this->Visao->setTemplate('Relatorio/tabela/usuarios');
				$this->Visao->render(array(
					'dadosPagina' => $dadosPagina,
					'arrayAtaque' => $array,
					'dirPublic' => DIR_PUBLIC,
					'permissao' => $persmissao,
					'dirRoot' => DIR_ROOT,
					'classMenuAtivo' => 'relatorio'
				));
			} catch (Exception $e) {
				die('ERRO TWIG: ' . $e->getMessage());
			}
			*/
		} catch (Exception $e) {
			die('ERRO DADOS: '.$e->getMessage());
		}
	}
	
	public function ataque() {
		$dadosPagina = $ataques = $persmissao = array();
	
		$persmissao['alterar'] = true;
		$persmissao['excluir'] = true;
		$persmissao['inserir'] = true;
	
	
		$aDados = new AtaqueDados;
	
		try {
			$arrayAtaque = $aDados->relatorioUsuario();
				
			foreach ($arrayAtaque as $a) {
				$ataques[] = array(
						'nome' => $a->__get('Usuario')->__get('nome'),
						'dataEntrada' => $a->__get('Usuario')->__get('dataEntrada'),
						'operacao' => $a->__get('Operacao')->__get('nome'),
						'dataOperacao' => $a->__get('Operacao')->__get('data'),
						'valor' => $a->__get('valor')
				);
			}
	
			try {
				$this->Visao->setTemplate('Relatorio/tabela/ataques');
				$this->Visao->render(array(
						'dadosPagina' => $dadosPagina,
						'ataques' => $ataques,
						'permissao' => $persmissao,
						'dirPublic' => DIR_PUBLIC,
						'dirRoot' => DIR_ROOT,
						'classMenuAtivo' => 'relatorio'
				));
			} catch (Exception $e) {
				die ('ERRO TWIG: ' . $e->getMessage());
			}
		} catch (Exception $e) {
			die ('ERRO DADOS: ' . $e->getMessage());
		}
	}
	
}