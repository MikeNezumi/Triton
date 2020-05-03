<?php
/* General DB Connector, common for all specified probers */
abstract class Db
{
	protected static $conn;
	/* DB settings */
	public static $options = [
		PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		PDO::MYSQL_ATTR_INIT_COMMAND =>  "SET NAMES utf8" ,
		PDO::ATTR_EMULATE_PREPARES   => false,
	];

	/* Connection to DB */
	static function connect($server, $user, $password)
	{
		if(!isset(self::$conn)){
			self::$conn = @ new PDO(
				"mysql:host=$server",
				$user,
				$password,
				self::$options
			);
		}
	}
}
