<?php
// Page.php - method loadPages() test
require("Page.php");
$page = new Page();
print_r($page->loadPages());
echo $page->lastError;
