<?php
	mb_internal_encoding("UTF-8");

	session_start();

	/* Autoloader of classes */
	function autoloadFunction($class)
	{
		/* Is it Model or Controller? */
		if(preg_match('/Db$/', $class)){
			require_once('Model/' . $class . '.php');
		}else{
			require_once('Controller/' . $class . '.php');
		}
	}

	/* UNDER CONSTRUCTION */
