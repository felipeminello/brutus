<?php
namespace Lib;

class Funcoes {
	/**
	 * Função para validação e conversão de datas
	 * @param string $str
	 * @param number $converter
	 * @return boolean|string
	 */
	public static function checkData($str, $converter = 0) {
		$data = substr($str, 0, 10);
	
		if (strpos($data, '/')) {
			$arrayData = explode('/', $data);
	
			$dia = $arrayData[0];
			$mes = $arrayData[1];
			$ano = $arrayData[2];
		} elseif (strpos($data, '-')) {
			$arrayData = explode('-', $data);
				
			$dia = $arrayData[2];
			$mes = $arrayData[1];
			$ano = $arrayData[0];
		} else {
			return false;
		}
	
		if (checkdate($mes, $dia, $ano)) {
			if ($converter == 1) {
				return $dia.'/'.$mes.'/'.$ano;
			} elseif ($converter == 2) {
				return $ano.'-'.$mes.'-'.$dia;
			} else {
				return $data;
			}
		} else
			return false;
	}
}