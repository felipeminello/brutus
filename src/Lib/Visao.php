<?php
namespace Lib;

use Twig_Loader_Filesystem;
use Twig_Environment;
use Exception;

class Visao {
	private $loader;
	private $twig;
	private $template;
	
	private $arrayJS;
	private $arrayCSS;
	
	function __construct() {
		$this->arrayJS = array(
				DIR_PUBLIC.'js/jquery.min.js',
				'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js'
		);
		
		$this->arrayCSS = array(
				array('src' => 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css', 'rel' => 'stylesheet', 'type' => null),
				array('src' => 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css', 'rel' => 'stylesheet', 'type' => null),
				array('src' => '//cdn.datatables.net/1.10.5/css/jquery.dataTables.css', 'rel' => 'stylesheet', 'type' => 'text/css'),
				array('src' => '//blueimp.github.io/Gallery/css/blueimp-gallery.min.css', 'rel' => 'stylesheet', 'type' => null),
		);
		
		try {
			$this->loader = new Twig_Loader_Filesystem(DIR_FIS_VISAO);
			$this->twig = new Twig_Environment($this->loader, array(
				'cache' => DIR_FIS_CACHE,
				'debug' => true,
				'auto_reload' => true,
				'strict_variables' => true,
				'autoescape'=> true
			));
		} catch (Exception $e) {
			throw $e;
		}
	}
	
	public function addJS($arquivo) {
		if (is_array($arquivo)) {
			foreach ($arquivo as $c => $v) {
				$this->addJS($v);
			}
		} else {
			array_push($this->arrayJS, $arquivo);
		}
	}
	
	public function addCSS(array $array) {
		array_push($this->arrayCSS, $array);
	}
	
	public function setTemplate($template) {
		try {
			$this->template = $this->twig->loadTemplate($template.'.twig');
		} catch (Exception $e) {
			throw $e;
		}
	}
	
	public function render(array $args = array()) {
		$admin = (isset($_SESSION['admin'])) ? $_SESSION['admin'] : array();
		
		$arrayArgs = $args + array('arrayJS' => $this->arrayJS, 'arrayCSS' => $this->arrayCSS, 'dirPublic' => DIR_PUBLIC, 'dirRoot' => DIR_ROOT, 'admin' => $admin);

		echo $this->template->render($arrayArgs);
	}
	
}