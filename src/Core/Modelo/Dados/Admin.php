<?php
namespace Core\Modelo\Dados;

use \PDO;
use Exception;

use Core\Modelo\Admin as a;
use Lib\Funcoes;

class Admin {
	protected $bd;
	
	public function __construct() {
		$this->bd = \Lib\BancoDados::get();
	}
	
	public function login(a $a) {
		$sql = "SELECT id, email, nome, senha as hashSenha FROM admin WHERE email = :email LIMIT 0,1";
		$stmt = $this->bd->prepare($sql);
		
		$stmt->bindValue('email', $a->__get('email'), PDO::PARAM_STR);
		
		if ($stmt->execute()) {
			$linha = $stmt->fetch(PDO::FETCH_ASSOC);
			
			if (is_array($linha)) {
				$a->receberDados($linha);
				
				return true;
			} else {
				throw new Exception('Nenhum admin encontrado');
			}
		} else {
			$array = $stmt->errorInfo();
			throw new Exception($array[2], $array[1]);
		}
	}
}