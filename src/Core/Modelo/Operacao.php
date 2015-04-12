<?php
namespace Core\Modelo;

use Exception;
use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\NestedValidationExceptionInterface;
use Lib\Funcoes;
use Core\Modelo\Dados\Operacao as OperacaoDados;

class Operacao {
	private $id;
	private $nome;
	private $data;
	private $finalizada;
	private $quantidadeAtaque;
	
	public function __construct(array $array = array()) {
		$this->receberDados($array);
	}
	
	public function __set($nome, $valor) {
		if (property_exists(get_class($this), $nome))
			$this->$nome = $valor;
	}
	
	public function __get($nome) {
		if (property_exists(get_class($this), $nome))
			return $this->$nome;
		else
			return false;
	}
	
	public function receberDados($array) {
		try {
			if (isset($array['id'])) {
				$this->id = (v::numeric()->positive()->length(1,10)->validate($array['id'])) ? $array['id'] : 0;
			}
	
			if (isset($array['nome'])) {
				$this->nome = (v::string()->length(1, 50)->validate($array['nome'])) ? $array['nome'] : null;
			}
				
			if (isset($array['data'])) {
				$this->data = (v::date('d/m/Y')->validate($array['data'])) ? $array['data'] : null;
			}
			
			if (isset($array['finalizada'])) {
				$this->finalizada = (v::numeric()->validate($array['finalizada'])) ? $array['finalizada'] : 0;
			}
		} catch(NestedValidationExceptionInterface $e) {
			throw $e;
		}
	}
	
	public function listarArray() {
		$oDados = new OperacaoDados;
		
		try {
			$arrayOperacao = $oDados->listar();
				
			$array = array();
				
			foreach($arrayOperacao as $o) {
				$array[$o->__get('id')] = $o->__get('nome').' - '.$o->__get('data');
			}
				
			return $array;
		} catch (Exception $e) {
			throw $e;
		}
	}
	
	public function listarArrayAtaques() {
		$oDados = new OperacaoDados();
		
		$array = array();
		
		try {
			$arrayOperacao = $oDados->listar();
				
			foreach ($arrayOperacao as $o) {
				$array[$o->__get('nome').' '.$o->__get('data')] = $o->__get('quantidadeAtaque');
			}
				
			return $array;
		} catch (Exception $e) {
			throw $e;
		}
	}
	
	public function arraySituacao() {
		$oDados = new OperacaoDados();
		
		$array = array();
		
		try {
			$arrayOperacao = $oDados->listar();
			
			foreach ($arrayOperacao as $o) {
				$array[$o->__get('nome').' '.$o->__get('data')] = $o->__get('finalizada') + 1;
			}
			
			return $array;
		} catch (Exception $e) {
			throw $e;
		}
	}
	
	public function validarDados() {
		if (empty($this->nome)) {
			throw new Exception('Preencha o nome da operação');
		} elseif (empty($this->data)) {
			throw new Exception('Preencha a data da operação');
		} else {
			return true;
		}
	}
	
	public function retornarArray() {
		return get_object_vars($this);
	}
}