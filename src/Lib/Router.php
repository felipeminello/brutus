<?php
namespace Lib;

use Exception;
/**
 * Router class.
 * Parses the request URL to controller and method. Checks is requested method is public.
 * Also create an specific model class.
 *
 * @author Denis Chekirda <dchekirda at gmail.com>
 * @version {0.1.2}
 */
class Router {

  /**
   * @var $param static string Param that sets after request url checked.
   */
	static $param;
  /**
   * @var $id static id Id that sets after request url checked. 
   */
	static $id;

  /**
   * Mapping requested URL with specified routes in routing list.
   *
   * @param $leo_routes array Array list of routes from routes config file.
   */
  static function start($leo_routes) {
    $step = explode('?', $_SERVER['REQUEST_URI']);
    $routes = explode('/', $step[0]);
	if ($routes[1] != '' && $routes[2] == '') {
		$request = $routes[1];
	} else {
		$request = $routes[1]. '/' .$routes[2];
	}
    if (array_key_exists($request, $leo_routes)) {
      foreach($leo_routes as $key=>$value) {
        if ($key == $request) {
          $controller = $value['controller'];
          $method = $value['method'];
          self::_prepareParams($routes);
          self::_prepareRoute($controller, $method);
        }
      }
    } else {
    	
      header("HTTP/1.0 404 Not Found");
      echo 'controle não encontrado';
    }
	}

  /**
   * Preparing controllerto be included. Checking is controller exists.
   * Creating new specific model instance. Creating controller instance.
   *
   * @param $controller string Controller name.
   * @param $method string Method name.
   */
  static function _prepareRoute($controller, $method) {
		$controller_path = DIR_FIS_CONTROLE . $controller . '.php';
		self::_checkControllerExists($controller_path);
    self::_createModelInstance($controller);
    self::_createInstance($controller, $method);
	}

  /**
   * Checks requested URL on params and id and if exists sets to the private vars.
   *
   * @param $routes array Requested URL.
   */
  static function _prepareParams($routes) {
    if ((!empty($routes[3]) && !empty($routes[4])) || !empty($routes[3]) || !empty($routes[4])) {
      self::$id = $routes[4];
      self::$param = $routes[3];
    }
  }

  /**
   * Checks is controller exists and inlcude it.
   *
   * @param $controller_path string Controller path. Used to include and controller.
   * @throws Exception
   */
  static function _checkControllerExists($controller_path) {
    try {
      if (file_exists($controller_path)) {
        require_once $controller_path;
      } else {
        throw new Exception;
      }
    } catch (Exception $e) {
      echo $e->getMessage();
    }
  }

  /**
   * Creating new instance that required by URL.
   *
   * @param $controller string Controller name.
   * @param $method string Method name.
   */
  static function _createInstance($controller, $method) {
//    $instance = new $controller;

  	$class = trim("Core\Controle\ ").$controller;
	$instance = new $class;

    if (method_exists($instance, $method)) {
      $reflection = new \ReflectionMethod($instance, $method);
      if (!$reflection->isPublic()) {
        @header('Location: error404');
      }
      $instance->$method(self::$param, self::$id);
    } else {
    	header("HTTP/1.0 404 Not Found");
    	echo 'metodo não encontrado';
//      @header('Location: error404');
    }
  }

  /**
   * Creates instance of model by requested controller.
   *
   * @param $controller string Controller name.
   */
  static function _createModelInstance($controller) {
    $model = DIR_FIS_MODELO . $controller . '.php';
    if(file_exists($model)) {
      require_once ($model);
    }
  }
}