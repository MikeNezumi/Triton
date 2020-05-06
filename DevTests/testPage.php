<?php
// Page.php - method loadPages() test
$page = new Page();
print_r($page->loadPages());
echo $page->lastError;

// Page.php - method loadWidgets() test
$widget = new Page();
$HTML = $widget->loadWidgets("Home");
echo $HTML;
echo $widget->lastError;

quote11 => Per ardua ad astra. - John James Ingalls, quote12 => Ex nihilo nihil fit. - Parmenides , quote13 => Palma non sine pulvere., picture41 => /IMAGES/Example1.png, table71 => /DOCUMENTS/PAPER/Deutsch1.xml, article14 => /DOCUMENTS/ARTICLE/ReturnOfThePrimitive1.txt, article64 => /DOCUMENTS/ARTICLE/TheRobberBarrons1.txt
