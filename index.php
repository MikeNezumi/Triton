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
    <title>Montreal Library | Triton</title>
    <link rel="stylesheet" href="universal.css">
  </head>
  <body>
    <div class="grid">
			<?php
			// Page.php - method loadWidgets() test
			$widget = new Page();
			$HTML = $widget->loadWidgets("E-Books");
			echo $HTML;
			 ?>
		</div>
	</body>
</html>
