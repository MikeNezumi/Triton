<?php

// Model/DbWriter - method insertRow() test
$inserter = new DbWriter();
$inserter->insertRow("article", ["headline" => "Search succesful!", "author" => "mike", "visibility" => "all", "docpath" => "DOCUMENTS/ARTICLE/SearchSuccesful.txt"]);

// Model/DbFileWriter - method discardDoc() test
$censor = new DbFileWriter();
echo $censor->discardDoc("Test1.txt") . "<br>";
echo $censor->lastError;

// Database/DbFileWriter - method uploadDoc();
$uploader = new DbFileWriter();
$data = [
  "headline" => "I am a filthy monster!",
  "author" => "Alan Greenspan",
  "visibility" => "all",
  "docpath" => ""
];
echo $uploader->uploadDoc("Greenspan.pdf", $data, "article") . "<br>";
echo $uploader->lastError;

  // in browser load Upload.php in DevTests, containing:
  echo "
  <form action=\"./index.php\" method=\"post\" enctype=\"multipart/form-data\">
    Select image to upload:
    <input type=\"file\" name=\"Greenspan.pdf\">
    <input type=\"submit\" value=\"Upload\">
  </form>
    ";
