<?php

// Model/DbWriter - method insertRow() test
$inserter = new DbWriter();
$inserter->insertRow("article", ["headline" => "Search succesful!", "author" => "mike", "visibility" => "all", "docpath" => "DOCUMENTS/ARTICLE/SearchSuccesful.txt"]);

// Model/DbFileWriter - method discardDoc() test
$censor = new DbFileWriter();
echo $censor->discardDoc("Test1.txt") . "<br>";
echo $censor->lastError;
