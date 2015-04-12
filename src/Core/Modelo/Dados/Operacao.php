<?php
namespace Core\Modelo\Dados;

use \PDO;
use Exception;

use Core\Modelo\Operacao as o;
use Core\Modelo\Core\Modelo;
use Lib\Funcoes;

class Operacao {
	protected $bd;
	
	public function __construct() {
		$this->bd = \Lib\BancoDados::get();
	}
	
	public function inserir(\Core\Modelo\Operacao $o) {
		$sql = "INSERT INTO operacao (nome, data, finalizada) VALUES (:nome, :data, :finalizada)";
		
		try {
			$stmt = $this->bd->prepare($sql);
			
			$stmt->bindValue('nome', $o->__get('nome'), PDO::PARAM_STR);
			$stmt->bindValue('data', Funcoes::checkData($o->__get('data'), 2), PDO::PARAM_STR);
			$stmt->bindValue('finalizada', $o->__get('finalizada'), PDO::PARAM_STR);
			
			if ($stmt->execute()) {
				$o->__set('id', $this->bd->lastInsertId());
				
				return true;
			} else {
				$array = $stmt->errorInfo();
				throw new Exception($array[2], $array[1]);
			}
		} catch (\PDOException $e) {
			throw $e;
		}
	}
	
	public function atualizar(\Core\Modelo\Operacao $o) {
		$sql = "UPDATE operacao SET nome = :nome, data = :data, finalizada = :finalizada WHERE id = :id";
		
		try {
			$stmt = $this->bd->prepare($sql);
			
			$stmt->bindValue('id', $o->__get('id'), PDO::PARAM_INT);
			$stmt->bindValue('nome', $o->__get('nome'), PDO::PARAM_STR);
			$stmt->bindValue('data', Funcoes::checkData($o->__get('data'), 2), PDO::PARAM_STR);
			$stmt->bindValue('finalizada', $o->__get('finalizada'), PDO::PARAM_STR);
			
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
	
	public function excluir(\Core\Modelo\Operacao $o) {
		$sql = "DELETE FROM operacao WHERE id = :id";
		
		try {
			$stmt = $this->bd->prepare($sql);
			
			$stmt->bindValue('id', $o->__get('id'), PDO::PARAM_INT);
			
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
	
	public function listar() {
		$sql = "SELECT o.id, o.nome, DATE_FORMAT(o.data, '%d/%m/%Y') AS data_f, o.finalizada, COUNT(a.id) AS total_ataque FROM operacao o LEFT JOIN (ataques a) ON a.id_operacao = o.id GROUP BY o.id ORDER BY o.data ASC";
		
		try {
			$stmt = $this->bd->query($sql);
	
			if ($stmt->execute()) {
				$array = array();
				
				$linhas = $stmt->fetchAll(PDO::FETCH_ASSOC);
				
				foreach ($linhas as $linha) {
					$linha['data'] = $linha['data_f'];
					
					$o = new o($linha);
					$o->__set('quantidadeAtaque', $linha['total_ataque']);
					
					$array[] = $o;
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
		$sql = "SELECT id, nome, DATE_FORMAT(data, '%d/%m/%Y') AS data, finalizada FROM operacao WHERE id = :id LIMIT 0,1";
		
		try {
			$stmt = $this->bd->prepare($sql);
			
			$stmt->bindValue('id', $id, PDO::PARAM_INT);
		
			if ($stmt->execute()) {
				$linha = $stmt->fetch(PDO::FETCH_ASSOC);
		
				if (is_array($linha)) {
					$o = new o($linha);
				} else {
					$o = new o();
				}
		
				return $o;
			} else {
				$array = $stmt->errorInfo();
				throw new Exception($array[2], $array[1]);
			}
		
		} catch (\PDOException $e) {
			throw $e;
		}
	}
}