<?php
namespace Core\Modelo\Dados;

use \PDO;
use Exception;

use Core\Modelo\Usuario as u;
use Lib\Funcoes;

class Usuario {
	protected $bd;
	
	public function __construct() {
		$this->bd = \Lib\BancoDados::get();
	}
	
	public function listar() {
		$sql = "SELECT id, nome, DATE_FORMAT(data_entrada, '%d/%m/%Y') AS dataEntrada, tipo FROM usuario ORDER BY nome ASC";
		
		try {
			$stmt = $this->bd->query($sql);
	
			if ($stmt->execute()) {
				$array = array();
				
				$linhas = $stmt->fetchAll(PDO::FETCH_ASSOC);
				
				foreach ($linhas as $linha) {
					$array[$linha['id']] = new u($linha);
				}
				
				return $array;
			} else {
				$array = $stmt->errorInfo();
				throw new Exception($array[2], $array[1]);
			}
				
		} catch (\PDOException $e) {
			throw $e;
		}
	}
	
	public function selecionar($id) {
		$sql = "SELECT id, nome, tipo, DATE_FORMAT(data_entrada, '%d/%m/%Y') AS dataEntrada FROM usuario WHERE id = :id LIMIT 0,1";
	
		try {
			$stmt = $this->bd->prepare($sql);
				
			$stmt->bindValue('id', $id, PDO::PARAM_INT);
	
			if ($stmt->execute()) {
				$linha = $stmt->fetch(PDO::FETCH_ASSOC);
	
				if (is_array($linha)) {
					$u = new u($linha);
				} else {
					$u = new u();
				}
	
				return $u;
			} else {
				$array = $stmt->errorInfo();
				throw new Exception($array[2], $array[1]);
			}
	
		} catch (\PDOException $e) {
			throw $e;
		}
	}
	
	public function inserir(u $u) {
		$sql = "INSERT INTO usuario (nome, data_entrada, tipo) VALUES (:nome, :data_entrada, :tipo)";
	
		try {
			$stmt = $this->bd->prepare($sql);
				
			$stmt->bindValue('nome', $u->__get('nome'), PDO::PARAM_STR);
			$stmt->bindValue('data_entrada', Funcoes::checkData($u->__get('dataEntrada'), 2), PDO::PARAM_STR);
			$stmt->bindValue('tipo', $u->__get('tipo'), PDO::PARAM_STR);
				
			if ($stmt->execute()) {
				$u->__set('id', $this->bd->lastInsertId());
	
				return true;
			} else {
				$array = $stmt->errorInfo();
				throw new Exception($array[2], $array[1]);
			}
		} catch (\PDOException $e) {
			throw $e;
		}
	}
	
	public function atualizar(u $u) {
		$sql = "UPDATE usuario SET nome = :nome, data_entrada = :data_entrada, tipo = :tipo WHERE id = :id";
	
		try {
			$stmt = $this->bd->prepare($sql);
				
			$stmt->bindValue('id', $u->__get('id'), PDO::PARAM_INT);
			$stmt->bindValue('nome', $u->__get('nome'), PDO::PARAM_STR);
			$stmt->bindValue('data_entrada', Funcoes::checkData($u->__get('dataEntrada'), 2), PDO::PARAM_STR);
			$stmt->bindValue('tipo', $u->__get('tipo'), PDO::PARAM_STR);
				
			if ($stmt->execute()) {
				return true;
			} else {
				$array = $stmt->errorInfo();
				throw new Exception($array[2], $array[1]);
			}
		} catch (\PDOException $e) {
			throw $e;
		}
	}
	
	public function excluir(u $u) {
		$sql = "DELETE FROM usuario WHERE id = :id";
	
		try {
			$stmt = $this->bd->prepare($sql);
				
			$stmt->bindValue('id', $u->__get('id'), PDO::PARAM_INT);
				
			if ($stmt->execute()) {
				return true;
			} else {
				$array = $stmt->errorInfo();
				throw new Exception($array[2], $array[1]);
			}
		} catch (\PDOException $e) {
			throw $e;
		}
	}
}