<?php
namespace Core\Controle;

use Respect\Rest\Routable;
use Exception;
use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\NestedValidationExceptionInterface;

use Core\Modelo\Dados\Ataque as AtaqueDados;
use Core\Modelo\Ataque as a;
use Core\Modelo\Usuario as u;
use Core\Modelo\Operacao as o;

class Ataque implements Routable {
	private $Visao;
	
	public function __construct() {
		$this->Visao = new \Lib\Visao;
	}
	
	public function get() {
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
	
	public function post() {
		$aDados = new AtaqueDados;
		$arrayValida = array('clear' => false);
	
		try {
			$a = new a($_POST);
				
			$a->validarDados();
	
			try {
				$aDados->inserir($a);
	
				$arrayValida['r'] = true;
				$arrayValida['m'] = 'Ataque inserido com sucesso';
	
			} catch (Exception $e) {
				$arrayValida['r'] = false;
				$arrayValida['m'] = $e->getMessage();
			}
		} catch (Exception $e) {
			$arrayValida['r'] = false;
			$arrayValida['m'] = $e->getMessage();
		}
	
		echo json_encode($arrayValida);
	}
	
	public function usuario($idUsuario) {
		$aDados = new AtaqueDados;
		$u = new u;
		$o = new o;
		
		try {
			$u->__set('id', $idUsuario);
			
			$arrayAtaque = $aDados->listarPeloUsuario($u);
			
			try {
				$this->Visao->setTemplate('Ataque/usuario');
				$this->Visao->render(array(						
						'ataques' => $arrayAtaque,
						'classMenuAtivo' => 'operacao'
				));
			} catch (Exception $e) {
				die ('ERRO TWIG: ' . $e->getMessage());
			}
		} catch (Exception $e) {
			die ('ERRO DADOS: '.$e->getMessage());
		}
	}
	
	public function cadastro($id = 0) {
		$aDados = new AtaqueDados;
		$u = new u;
		$o = new o;
		
		$dadosPagina = array();
		
		try {
			if (!empty($id)) {
				$a = $aDados->selecionar($id);
				$type = 'PUT';
			} else {
				$a = new a();
				$type = 'POST';
			}
			
			$id = $a->__get('id');
			
			$type = (!empty($id)) ? 'PUT' : 'POST';
			
			$dadosPagina['titulo'] = 'Inserir Ataque';
			
			$formOpcoes = array('type' => $type, 'action' => DIR_ROOT.'ataque', 'dataType' => 'json');
			
			$aArray = $a->retornarArray();
			
			$arrayUsuario = $u->listarArray();
			$arrayOperacao = $o->listarArray();
			
			try {
				$this->Visao->setTemplate('Ataque/cadastro');
				$this->Visao->render(array(						
						'dadosPagina' => $dadosPagina,
						'form' => $formOpcoes,
						'ataque' => $aArray,
						'arrayUsuario' => $arrayUsuario,
						'arrayOperacao' => $arrayOperacao,
						'dirPublic' => DIR_PUBLIC,
						'dirRoot' => DIR_ROOT,
						'classMenuAtivo' => 'operacao'
				));
			} catch (Exception $e) {
				die ('ERRO TWIG: ' . $e->getMessage());
			}
		} catch (Exception $e) {
			die ('ERRO DADOS: '.$e->getMessage());
		}
	}
}