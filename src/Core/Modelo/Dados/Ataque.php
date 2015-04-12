<?php
namespace Core\Modelo\Dados;

use \PDO;
use Exception;

use Core\Modelo\Operacao as o;
use Core\Modelo\Usuario as u;
use Core\Modelo\Ataque as a;

use Core\Modelo\Dados\Operacao as OperacaoDados;
use Core\Modelo\Dados\Usuario as UsuarioDados;

class Ataque {
	protected $bd;
	
	public function __construct() {
		$this->bd = \Lib\BancoDados::get();
	}
	
	public function inserir(\Core\Modelo\Ataque $a) {
		$sql = "INSERT INTO ataques (id_usuario, id_operacao, valor) VALUES (:id_usuario, :id_operacao, :valor)";
	
		try {
			$stmt = $this->bd->prepare($sql);
				
			$stmt->bindValue('id_usuario', $a->__get('Usuario')->__get('id'), PDO::PARAM_INT);
			$stmt->bindValue('id_operacao', $a->__get('Operacao')->__get('id'), PDO::PARAM_INT);
			$stmt->bindValue('valor', $a->__get('valor'), PDO::PARAM_STR);
				
			if ($stmt->execute()) {
				$a->__set('id', $this->bd->lastInsertId());
	
				return true;
			} else {
				$array = $stmt->errorInfo();
				throw new Exception($array[2], $array[1]);
			}
		} catch (\PDOException $e) {
			throw $e;
		}
	}
	
	
	public function relatorioUsuario() {
		$sql = "SELECT u.id AS u_id, u.nome AS u_nome, DATE_FORMAT(u.data_entrada, '%d/%m/%Y') AS u_dataEntrada,
				o.id AS o_id, o.nome AS o_nome, DATE_FORMAT(o.data, '%d/%m/%Y') AS o_data, a.id AS id, a.valor AS valor
				FROM usuario u LEFT JOIN (ataques a) ON u.id = a.id_usuario
				LEFT JOIN (operacao o) ON o.id = a.id_operacao
				GROUP BY u.id, o.id, a.valor
				ORDER BY u.nome, o.data";
		
		try {
			$stmt = $this->bd->query($sql);
	
			if ($stmt->execute()) {
				$array = array();
				
				$linhas = $stmt->fetchAll(PDO::FETCH_ASSOC);
				
				foreach ($linhas as $linha) {
					$u = new u(array('id' => $linha['u_id'], 'nome' => $linha['u_nome'], 'dataEntrada' => $linha['u_dataEntrada']));
					$o = new o(array('id' => $linha['o_id'], 'nome' => $linha['o_nome'], 'data' => $linha['o_data']));
					$a = new a(array('id' => $linha['o_id'], 'valor' => $linha['valor'], 'Usuario' => $u, 'Operacao' => $o));
					
					$array[] = $a;
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
	
	public function listarPorUsuario() {
		$sql = "SELECT u.id AS u_id, COUNT(a.id) AS total_ataque ";
		$sql .= "FROM usuario u LEFT JOIN (ataques a) ON u.id = a.id_usuario GROUP BY u.id ORDER BY u.nome";
		try {
			$stmt = $this->bd->query($sql);
	
			if ($stmt->execute()) {
				$array = array();
				
				$linhas = $stmt->fetchAll(PDO::FETCH_ASSOC);
				
				foreach ($linhas as $linha) {
					$array[$linha['u_id']] = array('quantidade' => $linha['total_ataque']);
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
	
	public function listarPeloUsuario(u $u) {
		$sql = "SELECT a.id AS id, a.valor AS valor, o.nome AS operacao, DATE_FORMAT(o.data, '%d/%m/%Y') AS data ";
		$sql .= "FROM ataques a INNER JOIN (operacao o) ON o.id = a.id_operacao WHERE a.id_usuario = :id_usuario GROUP BY a.id ORDER BY o.data ASC";
		try {
			$stmt = $this->bd->prepare($sql);
			
			$stmt->bindValue('id_usuario', $u->__get('id'), PDO::PARAM_INT);
	
			if ($stmt->execute()) {
				$array = array();
				
				$linhas = $stmt->fetchAll(PDO::FETCH_ASSOC);
				
				foreach ($linhas as $linha) {
					$array[] = array('operacao' => $linha['operacao'], 'data' => $linha['data'], 'valor' => $linha['valor']);
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
		$oDados = new OperacaoDados;
		$uDados = new UsuarioDados;
		
		$sql = "SELECT id, id_usuario, id_operacao, valor FROM ataques WHERE id = :id LIMIT 0,1";
	
		try {
			$stmt = $this->bd->prepare($sql);
				
			$stmt->bindValue('id', $id, PDO::PARAM_INT);
	
			if ($stmt->execute()) {
				$linha = $stmt->fetch(PDO::FETCH_ASSOC);
	
				if (is_array($linha)) {
					$o = $oDados->selecionar($linha['id_operacao']);
					$u = $uDados->selecionar($linha['id_usuario']);
					$a = new a($linha);
					$a->__set('Operacao', $o);
					$a->__set('Usuario', $u);
				} else {
					$a = new a();
				}
	
				return $a;
			} else {
				$array = $stmt->errorInfo();
				throw new Exception($array[2], $array[1]);
			}
	
		} catch (\PDOException $e) {
			throw $e;
		}
	}
}