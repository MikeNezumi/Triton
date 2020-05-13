<?php
class DbImage extends DbFileWriter
{
  /* File classesReader run exclusively for DOCUMENTS db */
  function __construct()
  {
    self::connect("MEDIA");
  }

  const IMG_EXTENSIONS = ["jpg", "jpeg", "png", "pdf", "webp", "xbm", "bmp", "ico"];
  const BIT_LIMIT = 8000000;  # 8 Mb upload limit

  /* --- uploadImage() ---
  Creates a record of a doc fom $_POST and places it in appropriate folder
  no naming standards */
  function uploadImage($formName, $data){
    // 'what could've gone wrong during upload' section:
    if (empty($_FILES[$formName])){
      $this->lastError = "No image was being uploaded. ~ uploadImage()";
      return false;
    } else if ($_FILES[$formName]["error"] != 0) {
      $this->lastError = "An error occured during uploading. ~ uploadImage()";
      return false;
    } else if ($_FILES[$formName]["size"] > self::BIT_LIMIT) {
      $this->lastError = "Image is too large to be uploaded. ~ uploadImage()";
      return false;
    }
    // verify data type
    $bits = explode(".", $_FILES[$formName]["name"]);
    $extension = end($bits);
    $extension = strtolower($extension);
    if (!in_array($extension, self::IMG_EXTENSIONS)) {
      $this->lastError = "Invalid file type. ~ uploadImage()";
      return false;
    }
    // standardize name and create path
    $newName = $_FILES[$formName]["name"];
    $allowed = array(" ");  # Reduce file name to alphabet characters
    $allowed = array_merge($allowed, range('A', 'Z'), range('a', 'z'), range(0, 9));
    $newName = str_split($newName);
    foreach ($newName as $key => $char) {  # Remove symbols from headline
      if (!in_array($char, $allowed)) {
        unset($newName[$key]);
      }
    }
    if (count($newName) == 0) {  # Check if there still is a name
      $this->lastError = "Invalid image name. ~ uploadImage()";
      return false;
    }
    $newName = implode($newName);  # Capitalize each word
    $newName = explode(" ", $newName);
    foreach ($newName as $key => $value) {
      $newName[$key] = strtolower($newName[$key]);
      $newName[$key] = ucfirst($newName[$key]);
    }
    $newName = implode($newName);
    $newName = rtrim($newName, $extension);
    $_FILES[$formName]["name"] = $newName . "." . $extension;
    //echo $_FILES[$formName]["name"];
    $path = "MEDIA/IMAGE/" . $_FILES[$formName]["name"];

    // supplying missing $data
    if ($data["docpath"] == false or $data["docpath"] == "") {
      $data["docpath"] = $path;
    }
    // attemp uploading file
    $fileStatus = move_uploaded_file($_FILES[$formName]["tmp_name"], $path);
    if ($fileStatus == false) {
      $this->lastError = "Failed to move the uploaded file. ~ uploadImage()";
      return false;
    }
    // attempt at creating record
    $status = $this->insertRow("image", $data);
    if ($status == false) {
      $this->lastError .= " used in uploadImage()";
      return false;
    }
    return true;
  }

  function discardImage($name){
    //delete the image
    $path = "MEDIA/IMAGE/$name";
    $fileStatus = unlink($path);
    if($fileStatus == false){
      $this->lastError = "File wasn't deleted. ~ discardDoc()";
      return false;
    }
    // check for its now empty record
    $recordStatus = $this->checkDb("../" . $path, "image");
    if ($this->lastError != "File of given path is recorded but doesn't exist. ~ checkDb()") {
      $this->lastError = "We've deleted an unrecorded file.";
      return true;
    }
    // set record straight and delete it from DB
    $this->lastError = "No recorded errors";
    $recordStatus = $this->delete("image", "docpath", $path,  "=");
    if ($recordStatus === false) {
      $this->lastError .= " used in discardDoc()";
      return false;
    } else {
      return true;
    }
  }
}
