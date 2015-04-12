<?php
namespace Core\Modelo;

use Exception;
use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\NestedValidationExceptionInterface;
use Core\Modelo\Usuario;
use Core\Modelo\Operacao;

class Ataque {
	private $id;
	private $valor;
	
	private $Usuario;
	private $Operacao;
	
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
	
			if (isset($array['valor'])) {
				$this->valor = (v::string()->length(1, 1)->validate($array['valor'])) ? $array['valor'] : 0;
			}
			
			if (isset($array['id_usuario'])) {
				if (v::numeric()->positive()->length(1,10)->validate($array['id_usuario'])) {
					$u = new Usuario;
					$u->__set('id', $array['id_usuario']);
				} else {
					$u = new Usuario;
				}
				$this->Usuario = $u;
			}
			
			if (isset($array['id_operacao'])) {
				if (v::numeric()->positive()->length(1,10)->validate($array['id_operacao'])) {
					$o = new Operacao;
					$o->__set('id', $array['id_operacao']);
				} else {
					$o = new Operacao;
				}
				$this->Operacao = $o;
			}
				
				
			if (isset($array['Usuario'])) {
				$this->Usuario = (v::instance('Core\Modelo\Usuario')->validate($array['Usuario'])) ? $array['Usuario'] : new Usuario;
			}
				
			if (isset($array['Operacao'])) {
				$this->Operacao = (v::instance('Core\Modelo\Operacao')->validate($array['Operacao'])) ? $array['Operacao'] : new Operacao;
			}
		} catch(NestedValidationExceptionInterface $e) {
			throw $e;
		}
	}
	
	public function validarDados() {
		$idUsuario = $this->Usuario->__get('id');
		$idOperacao = $this->Operacao->__get('id');
		
		if (empty($idUsuario)) {
			throw new Exception('Selecione o usuário');
		} elseif (empty($idOperacao)) {
			throw new Exception('Selecione a operação');
		} else {
			return true;
		}
	}
	
	public function retornarArray() {
		return get_object_vars($this);
	}
	
}