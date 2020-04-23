<?php

// Model/DbWriter - method insertRow() test
$inserter = new DbWriter("localhost", "root", "database", "DOCUMENTS");
$inserter->insertRow("article", ["headline" => "Search succesful!", "author" => "mike", "visibility" => "all", "docpath" => "DOCUMENTS/ARTICLE/SearchSuccesful.txt"]);

// Model/DbFileWriter - method discardDoc() test
$censor = new DbFileWriter("localhost", "root", "database", "DOCUMENTS");
echo $censor->discardDoc("Test1.txt") . "<br>";
echo $censor->lastError;
