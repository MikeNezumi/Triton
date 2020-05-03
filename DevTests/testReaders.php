<?php
// Model/DbReader - method probeAll() test
$reader = New DbReader();
$all_tables = $reader->probeAll();
foreach($all_tables as $table_name => $one_table) {
  foreach ($one_table as $id => $columns) {
    foreach ($columns as $value) {
      echo $value." ";
    }
    echo "<br>";
  }
  echo "<br>";
}

// Model/DbFileReader - method getTable() test
require("DbWriter.php");
$inserter = new DbWriter();
$inserter->insertRow("article", ["headline" => "Search succesful!", "author" => "mike", "visibility" => "all", "docpath" => "/DOCUMENTS/ARTICLE/SearchSuccesful.txt"]);
$detective = new DbFileReader();
echo $detective->getTable("/DOCUMENTS/ARTICLE/SearchSuccesful.txt");
echo $detective->getTable("SearchSuccesful.txt");


// Model/DbFileReader - method readDoc() test of .pdf file
$reader = new DbFileReader();
echo $reader->readDoc(false, "TheRomanticManifesto1.pdf", false, 10000);
echo $reader->lastError;

// Model/DbFileReader - method readDoc() test of .rtf file
$reader = new DbFileReader();
echo $reader->readDoc(false, "Sample1.rtf", false, 10000);
echo $reader->lastError;

// Model/DbFileReader - method readDoc() test of .docx file
$reader = new DbFileReader();
echo $reader->readDoc(false, "Constitution1.docx", false, 10000);
echo $reader->lastError;

// Model/DbFileReader - method readDoc() test of .docm file
$reader = new DbFileReader();
echo $reader->readDoc(false, "BillOfRights1.docm", false, 10000);
echo $reader->lastError;

// Model/DbFileReader - method readDoc() test of .odt file
$reader = new DbFileReader();
echo $reader->readDoc(false, "Declaration1.odt", false, 10000);
echo $reader->lastError;

// Model/DbFileReader - method readDoc() test of .xml file
$reader = new DbFileReader();
echo $reader->readDoc(false, "Deutsch1.xml");
echo $reader->lastError;


// Model/DbFileReader - method checkDb()
$reader = new DbFileReader();
echo $checker->CheckDb("/DOCUMENTS/ARTICLE/Test1.txt");
echo $checker->lastError;

// Model/DbFileReader - method downloadDoc()
$reader = new DbFileReader();
$downloader->downloadDoc($_POST["file_name"])

htmlspecialchars(<form action="DbFileReader.php" method="post" name="downloadform">
  <input type="file_name" value="Declaration1.odt", type="hidden">
  <input type="submit" value="Download the file">
</form>);
