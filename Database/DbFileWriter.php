<?php
/* Class for writing document files and corresponding records */
class DbFileWriter extends DbFileReader
{
  /* File classes run exclusively for DOCUMENTS db */
  function __construct()
  {
    self::connect("DOCUMENTS");
  }

  const BIT_LIMIT = 8000000;  # 8 Mb upload limit
  /*   --- uploadDoc() ---
  Creates a record of a doc fom $_POST and places it in appropriate folder,
  doesn't permit uploading duplicates, supplies standardized filename
  $formName is the name in HTML form, which sends file here
  $data is an assoc. array dependent on $form (article/paper)
    $data["path"] may not be pre-composed
  $form for recent_paper is just "paper" */
  function uploadDoc($formName, $data, $form = "article"){  # NOT TESTED PRIOR TO FRONT-END!
    // what could've gone wrong during upload' section:
    if (empty($_FILES[$formName])){
      $this->lastError = "No file was being uploaded. ~ uploadDoc()";
      return false;
    } else if ($_FILES[$formName]["error"] != 0) {
      $this->lastError = "An error occured during uploading. ~ uploadDoc()";
      return false;
    } else if ($_FILES[$formName]["size"] > self::BIT_LIMIT) {
      $this->lastError = "File is too large to be uploaded. ~ uploadDoc()";
      return false;
    }
    // verify data type
    $bits = explode(".", $_FILES[$formName]["name"]);
    $extension = end($bits);
    if (!in_array($extension, self::DOC_EXTENSIONS)) {
      $this->lastError = "Invalid file type. ~ uploadDoc()";
      return false;
    }
    // create valid path
    if ($form == "paper") {
      $path = $this->makePath($form, $data["topic"], $data["version"], $extension, true);
    } else if ($form == "article") {
      $path = $this->makePath($form, $data["headline"], 1, $extension, true);
    }
    $path = substr($path, 1);
    try {
      $newBits = explode("/", $path);
      $newName = end($newBits);
      $_FILES[$formName]["name"] = $newName;
    } catch (\Exception $e) {
      error_log("In DbFileWriter => uploadDoc(): " . $e);
      $this->lastError .= " used in uploadDoc()";
      return false;
    }
    // supplying missing $data
    if ($data["docpath"] == false) {
      $data["docpath"] = $path;
    }
    // attemp uploading file
    $fileStatus = move_uploaded_file($_FILES[$formName]["tmp_name"], $path);
    if ($fileStatus == false) {
      $this->lastError = "Failed to move the uploaded file. ~ uploadDoc()";
      return false;
    }
    // attempt at creating record
    $status = $this->insertRow($form, $data);
    if ($status == false) {
      $this->lastError .= " used in uploadDoc()";
      return false;
      // copy for recent_paper
    } else if ($form == "paper") {
      $status = $this->insertRow("recent_paper", $data);
      if ($status == false) {
        $this->lastError .= " used in uploadDoc()";
        return false;
    }
    return true;
  }
}

  /*    --- discardDoc() ---
  Deletes a document and its corresponding record from DB
  if $history == true, all older versions of document are deleted as well
  if $all is true, the most recent gets deleted too*/
  function discardDoc($name, $table = false, $history = false, $all = false){
    // getting/checking document's table
    $table = $this->getTable($name, $table);
    if ($table === false) {
      $this->lastError .= " used in discardDoc()";
      return false;
    }
    // discarding single file
    if ($history === false) {
      // erase the file itself
      $path = "/DOCUMENTS/" . strtoupper($table) . "/$name";
      $fileStatus = unlink(".." . $path);
      if($fileStatus == false){
        $this->lastError = "File wasn't deleted. ~ discardDoc()";
        return false;
      }
      // check for its now empty record
      $recordStatus = $this->checkDb(".." . $path);
      if ($this->lastError != "File of given path is recorded but doesn't exist. ~ checkDb()") {
        $this->lastError = "We've deleted an unrecorded file.";
        return true;
      }
      // set record straight and delete it from DB
      $this->lastError = "No recorded errors";
      $recordStatus = $this->delete($table, "docpath", $path,  "=");
      if ($recordStatus === false) {
        $this->lastError .= " used in discardDoc()";
        return false;
      } else {
        return true;
      }
    }
    // discarding multiple file versions
    else {
      /* UNDER CONSTRUCTION */
    }
  }
}
