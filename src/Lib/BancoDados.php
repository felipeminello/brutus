<?php
namespace Lib;

use \PDO;

class BancoDados extends PDO {

	/**
	 * @var array Array of saved databases for reusing
	 */
	protected static $instances = array();

	/**
	 * Static method get
	 *
	 * @param  array $group
	 * @return \helpers\database
	*/
	public static function get ($group = false) {
		// Determining if exists or it's not empty, then use default group defined in config
		$group = !$group ? array (
				'type' => DB_TIPO,
				'host' => DB_LOCAL,
				'name' => DB_DATABASE,
				'user' => DB_USER,
				'pass' => DB_PASSW
		) : $group;

		// Group information
		$type = $group['type'];
		$host = $group['host'];
		$name = $group['name'];
		$user = $group['user'];
		$pass = $group['pass'];

		// ID for database based on the group information
		$id = md5("$type.$host.$name.$user.$pass");

		// Checking if the same
		if(isset(self::$instances[$id])) {
			return self::$instances[$id];
		}

		try {
			// I've run into problem where
			// SET NAMES "UTF8" not working on some hostings.
			// Specifiying charset in DSN fixes the charset problem perfectly!
			$instance = new BancoDados("$type:host=$host;dbname=$name;charset=utf8", $user, $pass);
			
	//		$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//			$instance->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			
			$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				
			// Setting Database into $instances to avoid duplication
			self::$instances[$id] = $instance;
				
			return $instance;
		} catch(PDOException $e){
			throw $e;
		}
	}
}
