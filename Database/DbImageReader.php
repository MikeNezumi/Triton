<?php
class DbImageWriter extends DbFileWriter
{
  /* File classesReader run exclusively for DOCUMENTS db */
  function __construct()
  {
    self::connect("DOCUMENTS");
  }

  const IMG_EXTENSIONS = ["jpg", "jpeg", "png", "pdf", "webp", "xbm", "bmp", "ico"];
  const BIT_LIMIT = 8000000;  # 8 Mb upload limit

  /* --- uploadImage() ---
  Creates a record of a doc fom $_POST and places it in appropriate folder
  $name is not processed any further, must be already standardized
  $data is an assoc. array dependent on $form (article/paper) */
  function uploadImage(){
    // what could've gone wrong during upload' section:
    if (empty($_FILES[$name])){
      $this->lastError = "No file was being uploaded. ~ uploadDoc()";
      return false;
    } else if ($_FILES[$name]["error"] != 0) {
      $this->lastError = "An error occured during uploading. ~ uploadDoc()";
      return false;
    } else if ($_FILES[$name]["size"] > BIT_LIMIT) {
      $this->lastError = "File is too large to be uploaded. ~ uploadDoc()";
      return false;
    }
  }

  function discardImage(){

  }
}
