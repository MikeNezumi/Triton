<?php
	mb_internal_encoding("UTF-8");

	session_start();

	/* Autoloader of classes */
	function autoloadFunction($class)
	{
		/* Is it Model or Controller? */
		if (preg_match("/Db/", $class)){
			include_once("Database/" . $class . ".php");
		} else if (preg_match("/Widget/", $class)) {
			include_once("Widget/" . $class . ".php");
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
				// Page.php - method loadPages() test
				$page = new Page();
				$page->loadPages();
				/*
				#  Displaying each widget on page:
				foreach ($variable as $key => $value) {
					// code...
				}*/
			 ?>
		</div>
	</body>
</html>
