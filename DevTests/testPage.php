<?php
// Page.php - method loadPages() test
require("Page.php");
$page = new Page();
echo $page->loadPages()[0];
