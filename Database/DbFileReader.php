<?php
/* Prober for the purpose of saving and loading files from DOCUMENTS */
class DbFileReader extends DbWriter
{
  /* File classes run exclusively for DOCUMENTS db */
  function __construct($server, $user, $password)
  {
    parent::__construct($server, $user, $password, "DOCUMENTS");
    self::connect($server, $user, $password, "DOCUMENTS");
  }

  const DOC_EXTENSIONS = ["txt", "html", "rtf", "doc", "docm", "docx", "odt", "epub", "xml", "pdf"];
  const DOC_TABLES = ["recent_paper", "paper", "article"];


  /*    --- getTable() ---
  Searches document through tables by its name or docpath (often from makePath()
  If $checkedTable is unspecified, returns first found in order:
    recent_paper, paper, article
  If $justChecking == true, doesn't record into $lastError
  */
  protected function getTable($nameOrPath, $checkedTable = false, $justChecking = false)
    {
    $tables = self::DOC_TABLES;  # get array of tables
    if ($checkedTable !== false) {
      if (in_array($checkedTable, $tables)) {
        $tables = [$checkedTable];
      } else {
        if ($justChecking == false) {  # non-existing record counts as error
          $this->lastError = "There is no table $checkedTable in DOCUMENTS. ~ getTable()";
        }
        return false;
      }
    }
    foreach ($tables as $table) {
      if (substr($nameOrPath, 0, 10) != "/DOCUMENTS") {  # nameOrPath is name
        if ($table == "recent_paper") {
          $docpath = "/DOCUMENTS/PAPER/$nameOrPath";
        } else {
          $docpath = "/DOCUMENTS/" . strtoupper($table) . "/$nameOrPath";
        }
      } else {
        $docpath = $nameOrPath;
      }
      $stmt = self::$conn->prepare("SELECT count(*) FROM $table WHERE docpath = ?;");
      $stmt->execute([$docpath]);
      if ($stmt->fetchColumn() > 0) {
        return $table;
      }
    }
    if ($justChecking == false) {  # non-existing record counts as error
      if ($checkedTable === false) {
        $checkedTable = "any table";
      }
      $this->lastError = "Document $nameOrPath not recorded in $checkedTable. ~ getTable()";
    }
    return false;
  }


  /*    --- makePatht() ---
  Converts the name-version combination in a document into a valid docpath
  examples:
    What a marvelous heading! >>> DOCUMENTS/ARTICLE/WhatAMarvellousHeading.pdf
    !%^*@^#*Ab)*&@ >>> DOCUMENTS/PAPER/Ab.txt */
  function makePath($table, $headline, $version = 1, $extension = "txt", $new = false){
    $path = "/DOCUMENTS";
    if ($table == "recent_paper" or $table == "paper") {
      $path .= "/PAPER";
    } else if ($table == "article") {
      $path .= "/ARTICLE";
    } else {
      $this->lastError = "There is no table $table in DOCUMENTS. ~ makePath()";
      return false;
    }
    $allowed = array(" ");  # Reduce file name to alphabet characters
    $allowed = array_merge($allowed, range('A', 'Z'), range('a', 'z'));
    $headline = str_split($headline);
    foreach ($headline as $key => $char) {  # Remove symbols from headline
      if (!in_array($char, $allowed)) {
        unset($headline[$key]);
      }
    }
    if (count($headline) == 0) {  # Check if there still is a name
      $this->lastError = "Invalid headline. ~ makePath()";
      return false;
    }
    $headline = implode($headline);  # Capitalize each word
    $headline = explode(" ", $headline);
    foreach ($headline as $key => $value) {
      $headline[$key] = strtolower($headline[$key]);
      $headline[$key] = ucfirst($headline[$key]);
    }
    $headline = implode($headline);
    $path .= "/$headline";
    $extension = strtolower($extension);  # Append file extension
    if (!in_array($extension, self::DOC_EXTENSIONS)) {
      $this->lastError = "Extension .$extension is not suported. ~ makePath()";
      return false;
    } else {  # test for duplicate entry
      $path .= "$version.$extension";
      if ($this->getTable($path, true) == $table and $new == true) {
        $this->lastError = "This file already exists. ~ makePath()";
        return false;
      } else {
        return $path;
      }
    }
  }


  /*    --- getDocInfo() ---
  If specific path is known, supply only path (it contains the others)
  assumes document name in a correct format!
  all parameters are ultimately needed, but can be deduced
  limit is number of characters

  returns assoc. array, eg.:
      "docpath" => /DOCUMENTS/PAPER/Electromagnetism1.pdf
        "table" => "paper"
         "name" => "Electromagnetism1.pdf"
    "extension" => "pdf"  */
  function getDocInfo($path = false, $name = false, $table = false){
    // checking whether deduction is possible
    if ($path === false and $name === false) {
      $this->lastError = "Neither path nor name given. ~ getDocInfo()";
      return false;
    }
    // deducing missing $name
    if ($name === false) {
      $pathArray = explode("/",$path);
      $name = end($pathArray);  # chopping off file $name
    }
    // finding and verifying $table and $name
    $table = $this->getTable($name, $table);
    if ($table == false) {
      $this->lastError .= " in getDocInfo()";  # expands error record created by getTable()
      return false;
    }
    // creating $path
    $folder = strtoupper($table);
    if (in_array($folder,["RECENT_PAPER", "RECENT_PAPER", "RECENTPAPER"])) {
      $folder = "PAPER";  #files from recent_paper database are in PAPER folder
    }
    $path = "/DOCUMENTS/". $folder . "/$name";  # simplified makePath()

    // checking path
    $row = $this->probe($table, "docpath", "docpath = \"$path\"");
    if (empty($row)) {
      $this->lastError = "Such document isn't recorded. ~ getDocInfo()";
      return false;
    }
    // reading file
    $choppedName = explode(".", $name);
    $extension = end($choppedName);
    if (!in_array($extension, self::DOC_EXTENSIONS)) {
      $this->lastError = "Document isn't of a supported format. ~ getDocInfo()";
      return false;
    }
    return [
      "path" => $path,
      "table" => $table,
      "name" => $name,
      "extension" => $extension
    ];
  }


  /*    --- readDoc() ---
  Returns string - human text of a document (or part of it, bound by character $limit)
  uses getDocInfo to find and verify document */
  function readDoc($path = false, $name = false, $table = false, $limit = false){
    $docInfo = $this->getDocInfo($path, $name, $table);  # getting doc's docpath, table, name & ext.
    if ($docInfo === false) {
      $this->lastError .= " used in readDoc";  # specifying error message getDocInfo() throwed
      return false;
    }
    try {
      $path = $docInfo["path"];
      $table = $docInfo["table"];
      $name = $docInfo["name"];
      $extension = $docInfo["extension"];
    } catch (\Exception $e) {
      $lastError = "Document metadata couldn't be gathered properly. ~ readDoc()";
      error_log("In DbFileReader->readDoc: ". $e);
      return false;
    }

    // regular cases - getting content via PHPWord
    if (in_array($extension, ["docm", "docx"])) {  # Fix odt, add rtf when phpWord supports it
      $content = "";
      try {
        require_once("../vendor/phpoffice/phpword/bootstrap.php");
        $phpWord = \PhpOffice\PhpWord\IOFactory::load(".." . $path);
        $sections = $phpWord->getSections();
        foreach ($sections as $key => $value) {
          $sectionElement = $value->getElements();
          foreach ($sectionElement as $elementKey => $elementValue) {
            if ($elementValue instanceof \PhpOffice\PhpWord\Element\TextRun) {
              $secondSectionElement = $elementValue->getElements();
              foreach ($secondSectionElement as $secondSectionElementKey => $secondSectionElementValue) {
                if ($secondSectionElementValue instanceof \PhpOffice\PhpWord\Element\Text) {
                  $content .= $secondSectionElementValue->getText();
                }
                if ($limit !== false and strlen($content) > $limit) {
                  return substr($content, 0, $limit);
                }
              }
            }
          }
        }
      return $content;
      } catch (\Exception $e) {
        error_log("In ReadDocument, while reading $name: " . $e);
        $this->lastError = "Failed to open $name. ~ readDoc()";
        return false;
      }
    // special cases - unique method for each
    } else {
      switch ($extension) {  # reading methods vary depending on file type
        case "txt":
        case "html":
        if ($limit === false) {
          $content = file_get_contents(".." . $path);
        } else {
          $content = file_get_contents(".." . $path, false, NULL, 0, $limit);
        }
          if ($content === false) {
            $this->lastError = "The file $name is not in $folder folder. ~ readDoc()";
            return false;
          }
          return $content;

        case "doc":
          try {
            if(($fh = fopen("../".$path, 'r')) !== false) {
              $headers = fread($fh, 0xA00);
              $n1 = (ord($headers[0x21C]) - 1 );  # 0 to 255 characters
              $n2 = ((ord($headers[0x21D]) - 8) * 256);  # 256 to 63743 chars
              $n3 = ((ord($headers[0x21E]) * 256) * 256);  # 63744 to 16775423 chars
              $n4 = (((ord($headers[0x21F]) * 256) * 256) * 256);  # 16775424 to 4294965504 chars
              $textLength = ($n1 + $n2 + $n3 + $n4);  # Total length of text in the document
              $content = fread($fh, $textLength);
              $content = mb_convert_encoding($content,'UTF-8');
              if ($limit === false) {
                return $content;
              } else {
                return substr($content, 0, $limit);
              }
            } else {
              $this->lastError = "Failed to open $name. ~ readDoc()";
              return false;
            }
          } catch (\Exception $e) {
            error_log("In ReadDocument, while reading $name: " . $e);
            $this->lastError = "Failed to open $name. ~ readDoc()";
            return false;
          }
        case "epub":
          $this->lastError = "Triton supports .epub, but can't look inside. ~ readDoc()";
          return false;
        case "pdf":
          require("../vendor/autoload.php");
            // Parse pdf file and build necessary objects.
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile(".." . $path);
            // Retrieve all pages from the pdf file.
            if ($limit === false) {
              return $pdf->getText();
            } else {
              return substr($pdf->getText(), 0, $limit);
            }
        default:
          $this->lastError = ".$extension docs are supported, but can't yet be processed. ~ readDoc()";
          return false;
      }
    }
  }

  /*    --- checkDb() ---
  Goes through DOCUMENTS and databases and handles dissonances
  If no docpath is specified, performs a non-interruptive general scan and informs admin of any problems (once a week/day/month)
  Otherwise returns false (breaks the process) if either file or matching record is missing*/
  function checkDb($docpath = false){
    if ($docpath === false) {
      // general maintainance check, under construction
      // also, shifts to and from recent_paper are made here
    } else {
      $docInfo = $this->getDocInfo($docpath);  # checks for DB record
      if ($docInfo === false) {
        $this->lastError .= " used in checkDb()";
        return false;
      }
      if (file_exists(".." . $docpath)) {  # check for file
        return true;
      } else {
        $this->lastError = "File of given path is recorded but doesn't exist. ~ checkDb()";
        return false;
      }
    }
  }
}
