<?php
namespace Core\Modelo;

use Exception;
use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\NestedValidationExceptionInterface;
use Core\Modelo\Dados\Usuario as UsuarioDados;

class Usuario {
	private $id;
	private $nome;
	private $tipo;
	private $dataEntrada;
	
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
	
	public function getTipo() {
		if ($this->tipo == 'm') {
			$tipo = 'Membro';
		} elseif ($this->tipo == 'o') {
			$tipo = 'Oficial';
		} elseif ($this->tipo == 'l') {
			$tipo = 'Líder';
		} else {
			$tipo = null;
		}
		
		return $tipo;
	}
	
	public function receberDados($array) {
		try {
			if (isset($array['id'])) {
				$this->id = (v::numeric()->positive()->length(1,10)->validate($array['id'])) ? $array['id'] : 0;
			}
	
			if (isset($array['nome'])) {
				$this->nome = (v::string()->length(1, 50)->validate($array['nome'])) ? $array['nome'] : null;
			}
				
			if (isset($array['dataEntrada'])) {
				$this->dataEntrada = (v::date('d/m/Y')->validate($array['dataEntrada'])) ? $array['dataEntrada'] : null;
			}
				
			if (isset($array['tipo'])) {
				$this->tipo = (v::string()->length(1,1)->validate($array['tipo'])) ? $array['tipo'] : null;
			}
		} catch(NestedValidationExceptionInterface $e) {
			throw $e;
		}
	}
	
	public function listarArray() {
		$uDados = new UsuarioDados;
		
		try {
			$arrayUsuario = $uDados->listar();
			
			$array = array();
			
			foreach($arrayUsuario as $u) {
				$array[$u->__get('id')] = $u->__get('nome');
			}
			
//			asort($array);
			
			return $array;
		} catch (Exception $e) {
			throw $e;
		}
	}
	
	public function validarDados() {
		if (empty($this->nome)) {
			throw new Exception('Preencha o nome do usuário');
		} elseif (empty($this->dataEntrada)) {
			throw new Exception('Preencha a data de entrada do usuário');
		} elseif (empty($this->tipo)) {
			throw new Exception('Preencha o tipo do usuário');
		} else {
			return true;
		}
	}
	
	
	public function retornarArray() {
		return get_object_vars($this);
	}
}