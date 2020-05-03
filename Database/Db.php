<?php
/* General DB Connector, common for all specified probers */
abstract class Db
{
	var $server = "localhost";
	var $user = "root";
	var $password = "database";

	protected static $conn;
	/* DB settings */
	public static $options = [
		PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		PDO::MYSQL_ATTR_INIT_COMMAND =>  "SET NAMES utf8" ,
		PDO::ATTR_EMULATE_PREPARES   => false,
	];

	/* Connection to DB */
	static function connect($database)
	{
		if(!isset(self::$conn)){
			self::$conn = @ new PDO(
				"mysql:host=$this->server; dbname=$database",
				$this->user,
				$this->password,
				self::$options
			);
		}
	}
}
