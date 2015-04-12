<?php
namespace Core\Controle;

use Respect\Rest\Routable;
use Exception;
use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\NestedValidationExceptionInterface;

use Core\Modelo\Dados\Operacao as OperacaoDados;
use Core\Modelo\Operacao as o;

class Operacao implements Routable {
	private $Visao;
	
	public function __construct() {
		$this->Visao = new \Lib\Visao;
	}

	public function get() {
		$oDados = new OperacaoDados;
	
		$dadosPagina = $operacoes = $persmissao = array();
		
		$persmissao['alterar'] = true;
		$persmissao['excluir'] = true;
		$persmissao['inserir'] = true;
	
		try {
			$arrayOperacao = $oDados->listar();
				
			foreach ($arrayOperacao as $o) {
				$operacoes[$o->__get('id')] = array('id' => $o->__get('id'), 'nome' => $o->__get('nome'), 'data' => $o->__get('data'), 'finalizada' => $o->__get('finalizada'));
			}
				
			try {
				$this->Visao->setTemplate('Operacao/listar');
				$this->Visao->render(array(
						'permissao' => $persmissao,
						'dadosPagina' => $dadosPagina,
						'operacoes' => $operacoes,
						'classMenuAtivo' => 'operacao'
				));
			} catch (Exception $e) {
				die ('ERRO TWIG: ' . $e->getMessage());
			}
		} catch (Exception $e) {
			die ('ERRO DADOS: '.$e->getMessage());
		}
	}
	
	public function post() {
		$oDados = new OperacaoDados;
		$arrayValida = array('clear' => true);
		
		try {
			$o = new o($_POST);
			
			$o->validarDados();
		
			try {
				$oDados->inserir($o);
				
				$arrayValida['r'] = true;
				$arrayValida['m'] = 'Operação inserida com sucesso';
		
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
		$oDados = new OperacaoDados;
		$arrayValida = array('clear' => false);
		
		$arrayDados = array();
		
		try {
			parse_str(file_get_contents("php://input"), $arrayDados);
			
			$o = $oDados->selecionar($arrayDados['id']);
			
			$o->receberDados($arrayDados);
			
			$o->validarDados();
		
			try {
				$oDados->atualizar($o);
				
				$arrayValida['r'] = true;
				$arrayValida['m'] = 'Operação atualizada com sucesso';
		
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
		$oDados = new OperacaoDados;
		
		$arrayDados = array();
		
		try {
			parse_str(file_get_contents("php://input"), $arrayDados);
			
			$o = new o($arrayDados);
		
			try {
				$oDados->excluir($o);
				
				$arrayValida['r'] = true;
				$arrayValida['m'] = 'Operação excluída com sucesso';
		
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
		$oDados = new OperacaoDados;
		
		$dadosPagina = array();
		
		try {
			if (!empty($id)) {
				$o = $oDados->selecionar($id);
				$type = 'PUT';
			} else {
				$o = new o();
				$type = 'POST';
			}
			
			$id = $o->__get('id');
			
			$type = (!empty($id)) ? 'PUT' : 'POST';
			
			$dadosPagina['titulo'] = (!empty($id)) ? 'Alterar Operação' : 'Inserir Operação';
			
			$formOpcoes = array('type' => $type, 'action' => DIR_ROOT.'operacao/cadastro', 'dataType' => 'json');
			
			$oArray = $o->retornarArray();
			
			try {
				$this->Visao->setTemplate('Operacao/cadastro');
				$this->Visao->render(array(
					'dadosPagina' => $dadosPagina,
					'form' => $formOpcoes,
					'operacao' => $oArray,
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
	
	public function grafico() {
		$o = new o;
	
		$dadosPagina = array();
	
		try {
			$id = 0;
				
			$type = (!empty($id)) ? 'PUT' : 'POST';
			
			$operacoes = $o->listarArrayAtaques();
				
			$dadosPagina['titulo'] = (!empty($id)) ? 'Alterar Operação' : 'Grafico';
				
			$formOpcoes = array('type' => $type, 'action' => DIR_ROOT.'operacao/cadastro', 'dataType' => 'json');
				
			try {
				$this->Visao->setTemplate('Operacao/grafico');
				$this->Visao->render(array(
						'dadosPagina' => $dadosPagina,
						'form' => $formOpcoes,
						'operacoes' => $operacoes,
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