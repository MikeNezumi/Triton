<?php
	mb_internal_encoding("UTF-8");

	session_start();

	/* Autoloader of classes */
	function autoloadFunction($class)
	{
		/* Is it Model or Controller? */
		if (preg_match("/Db/", $class)){
			require_once("Database/" . $class . ".php");
		} else if (preg_match("/Widget/", $class)) {
			require_once('Widget/' . $class . ".php");
		}
		else {
			require_once($class . ".php");
		}
	}

	spl_autoload_register("autoloadFunction");
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>CSS Grid</title>
    <link rel="stylesheet" href="universal.css">
  </head>
  <body>
    <div class="grid">
			<?php
			$inserter = new DbWriter("localhost", "root", "database", "DOCUMENTS");
			$inserter->insertRow("article", ["headline" => "Search succesful!", "author" => "mike", "visibility" => "all", "docpath" => "DOCUMENTS/ARTICLE/SearchSuccesful.txt"]);
			echo $inserter->lastError;
				/*
				#  Displaying each widget on page:
				foreach ($variable as $key => $value) {
					// code...
				}*/
			 ?>
		</div>
	</body>
</html>
