<?php
namespace Core\Controle;

use Respect\Rest\Routable;
use Exception;
use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\NestedValidationExceptionInterface;

use Core\Modelo\Dados\Usuario as UsuarioDados;
use Core\Modelo\Usuario as u;

class Usuario implements Routable {
	private $Visao;
	
	public function __construct() {
		$this->Visao = new \Lib\Visao;
	}
	
	public function get() {
		$uDados = new UsuarioDados;
		
		$dadosPagina = $usuarios = $persmissao = array();
		
		$persmissao['alterar'] = true;
		$persmissao['excluir'] = true;
		$persmissao['inserir'] = true;
		
		try {
			$arrayUsuario = $uDados->listar();
			
			foreach ($arrayUsuario as $u) {
				$usuarios[$u->__get('id')] = array('id' => $u->__get('id'), 'nome' => $u->__get('nome'), 'dataEntrada' => $u->__get('dataEntrada'), 'tipo' => $u->__get('tipo'));
			}
			
			try {
				$this->Visao->setTemplate('Usuario/listar');
				$this->Visao->render(array(
					'dadosPagina' => $dadosPagina,
					'usuarios' => $usuarios,
					'dirPublic' => DIR_PUBLIC,
					'permissao' => $persmissao,
					'dirRoot' => DIR_ROOT,
					'classMenuAtivo' => 'usuario'
				));
			} catch (Exception $e) {
				die('ERRO TWIG: ' . $e->getMessage());
			}
		} catch (Exception $e) {
			die('ERRO DADOS: '.$e->getMessage());
		}
	}
	
	public function relatorio() {
		$uDados = new UsuarioDados;
		
		$dadosPagina = $usuarios = $persmissao = array();
		
		$persmissao['alterar'] = true;
		$persmissao['excluir'] = true;
		$persmissao['inserir'] = true;
		
		try {
			$arrayUsuario = $uDados->listar();
				
			foreach ($arrayUsuario as $u) {
				$usuarios[$u->__get('id')] = array('id' => $u->__get('id'), 'nome' => $u->__get('nome'), 'dataEntrada' => $u->__get('dataEntrada'), 'tipo' => $u->__get('tipo'));
			}
				
			try {
				$this->Visao->setTemplate('Usuario/listar');
				$this->Visao->render(array(
						'dadosPagina' => $dadosPagina,
						'usuarios' => $usuarios,
						'dirPublic' => DIR_PUBLIC,
						'permissao' => $persmissao,
						'dirRoot' => DIR_ROOT,
						'classMenuAtivo' => 'usuario'
				));
			} catch (Exception $e) {
				die('ERRO TWIG: ' . $e->getMessage());
			}
		} catch (Exception $e) {
			die('ERRO DADOS: '.$e->getMessage());
		}
	}
	
	public function post() {
		$uDados = new UsuarioDados;
		$arrayValida = array('clear' => true);
		
		try {
			$u = new u($_POST);
			
			$u->validarDados();
		
			try {
				$uDados->inserir($u);
				
				$arrayValida['r'] = true;
				$arrayValida['m'] = 'Usuário inserido com sucesso';
		
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
	
	public function put() {
		$uDados = new UsuarioDados;
		$arrayValida = array('clear' => false);
		
		$arrayDados = array();
		
		try {
			parse_str(file_get_contents("php://input"), $arrayDados);
			
			$u = new u($arrayDados);
					
			$u->validarDados();
		
			try {
				$uDados->atualizar($u);
				
				$arrayValida['r'] = true;
				$arrayValida['m'] = 'Usuário atualizado com sucesso';
		
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
	
	public function delete() {
		$uDados = new UsuarioDados;
		
		$arrayDados = array();
		
		try {
			parse_str(file_get_contents("php://input"), $arrayDados);
			
			$u = new u($arrayDados);
		
			try {
				$uDados->excluir($u);
				
				$arrayValida['r'] = true;
				$arrayValida['m'] = 'Usuário excluído com sucesso';
		
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
	
	public function cadastro($id = 0) {
		$uDados = new UsuarioDados;
		
		$dadosPagina = array();
		
		try {
			if (!empty($id)) {
				$u = $uDados->selecionar($id);
				$type = 'PUT';
			} else {
				$u = new u();
				$type = 'POST';
			}
			
			$id = $u->__get('id');
			
			$type = (!empty($id)) ? 'PUT' : 'POST';
			
			$dadosPagina['titulo'] = (!empty($id)) ? 'Alterar Usuário' : 'Inserir Usuário';
			
			$formOpcoes = array('type' => $type, 'action' => DIR_ROOT.'usuario', 'dataType' => 'json');
			
			$uArray = $u->retornarArray();
			
			try {
				$this->Visao->setTemplate('Usuario/cadastro');
				$this->Visao->render(array(
					'dadosPagina' => $dadosPagina,
					'form' => $formOpcoes,
					'usuario' => $uArray,
					'dirPublic' => DIR_PUBLIC,
					'dirRoot' => DIR_ROOT,
					'classMenuAtivo' => 'usuario'
				));
			} catch (Exception $e) {
				die ('ERRO TWIG: ' . $e->getMessage());
			}
		} catch (Exception $e) {
			die ('ERRO DADOS: '.$e->getMessage());
		}
	}	
}