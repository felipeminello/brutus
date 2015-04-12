<?php
namespace Core\Modelo;

use Exception;
use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\NestedValidationExceptionInterface;
use Lib\Funcoes;


class Admin {
	private $id;
	private $nome;
	private $email;
	private $hashSenha;
	private $senha;
	private $senhaConf;
	
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
				
			if (isset($array['email'])) {
				$this->email = (v::email()->validate($array['email'])) ? $array['email'] : null;
			}

			if (isset($array['senha'])) {
				$this->senha = (v::string()->validate($array['senha'])) ? $array['senha'] : null;
			}
					
			if (isset($array['hashSenha'])) {
				$this->hashSenha = (v::string()->length(60,60)->validate($array['hashSenha'])) ? $array['hashSenha'] : null;
			}
		} catch(NestedValidationExceptionInterface $e) {
			throw $e;
		}
	}
	
	public function checkLogin() {
		if (!isset($_SESSION['admin']) or !is_array($_SESSION['admin']) or empty($_SESSION['admin']['id'])) {
			throw new Exception('Não está logado');
		} else {
			return true;
		}
	}
	
	public function validarLogin() {
		if (empty($this->email)) {
			throw new Exception('Preencha o email');
		} elseif (empty($this->senha)) {
			throw new Exception('Preencha a senha');
		} else {
			return true;
		}
	}
	
	public function retornarArray() {
		return get_object_vars($this);
	}
	
	public function verificaSenha() {
		if (password_verify($this->senha, $this->hashSenha))
			return true;
		else
			throw new Exception('Senha inválida');
	}
	
	public function hashSenha($senha) {
		$options = array(
				'cost' => PASSWORD_COST,
				'salt' => mcrypt_create_iv(22, MCRYPT_DEV_URANDOM)
		);
	
		$this->__set('senha', password_hash($senha, PASSWORD_BCRYPT, $options));
	}
	
}