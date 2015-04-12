<?php
namespace Core\Controle;

// use Respect\Rest\Routable;
use Exception;
use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\NestedValidationExceptionInterface;

use Core\Modelo\Dados\Admin as AdminDados;
use Core\Modelo\Admin as a;

class Admin {
	private $Visao;
	
	public function __construct() {
		$this->Visao = new \Lib\Visao;
	}

	public function get() {
		try {
			$a = new a;
			
			$a->checkLogin();
			
			try {
				$this->Visao->setTemplate('Admin/home');
				$this->Visao->render(array(
						
						
						'classMenuAtivo' => false
				));
			} catch (Exception $e) {
				die ('ERRO TWIG: ' . $e->getMessage());
			}
		} catch (Exception $e) {
			$this->login();
		}
	}
	
	public function login() {
		$dadosPagina['titulo'] = 'Login';
			
		$formOpcoes = array('type' => 'POST', 'action' => DIR_ROOT.'admin', 'dataType' => 'json');
		
		try {
			$this->Visao->setTemplate('Admin/login');
			$this->Visao->render(array(
					'dirPublic' => DIR_PUBLIC,
					'dirRoot' => DIR_ROOT,
					'dadosPagina' => $dadosPagina,
					'form' => $formOpcoes,
					'classMenuAtivo' => null
			));
		} catch (Exception $e) {
			die('ERRO TWIG: ' . $e->getMessage());
		}
	}
	
	public function logout() {
		$dadosPagina['titulo'] = 'Logout';
			
		session_destroy();
		
		try {
			$this->Visao->setTemplate('Admin/logout');
			$this->Visao->render(array(
					'dadosPagina' => $dadosPagina,
					'classMenuAtivo' => 'login'
			));
		} catch (Exception $e) {
			die('ERRO TWIG: ' . $e->getMessage());
		}
	}
	
	public function post() {
		$aDados = new AdminDados;
		$arrayValida = array('clear' => true);
		
		try {
			$a = new a($_POST);
			
			$a->validarLogin();
		
			try {
				$aDados->login($a);
				
				try {
					$a->verificaSenha();
					
					$_SESSION['admin']['id'] = $a->__get('id');
					$_SESSION['admin']['nome'] = $a->__get('nome');
					
					$arrayValida['r'] = true;
					$arrayValida['m'] = 'Login efetuado com sucesso';
			
				} catch (Exception $e) {
					$arrayValida['r'] = false;
					$arrayValida['m'] = $e->getMessage();
				}
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