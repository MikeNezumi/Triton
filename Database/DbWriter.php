<?php
/* Prober for the purpose of altering tables */
class DbWriter extends DbReader
{
  protected $sql = array(
    "human" => "INSERT INTO human (username, gender, email, password, phone, role, permissions)
                VALUES (:username, :gender, :email, :password, :phone, :role, :permissions)",

    "article" => "INSERT INTO article (headline, author, visibility, docpath)
                  VALUES (:headline, :author, :visibility, :docpath)",

    "paper" => "INSERT INTO paper (topic, course, author, license, version, docpath)
                VALUES (:topic, :course, :author, :license, :version, :docpath)",

    "recent_paper" => "INSERT INTO paper (topic, course, author, license, version, docpath)
                       VALUES (:topic, :course, :author, :license, :version, :docpath)",

    "image" => "INSERT INTO image (content, page, widget, docpath)
                       VALUES (:content, :page, :widget, :docpath)"
    );  # holds INSERT sql for each table, AUTO_INCREMENTed id omitted

    function __construct($database)
    {
      self::connect($database);
    }

  var $lastError = "No recorded errors";  # last error that occured using this object

  /* --- insertRow() ---
  Insert one or more COMPLETE rows (string/array of strings) into specified table
  $values, format: (quoting STRUCTURE.txt, AUTO_INCREMENT id omitted on purpose)
  ~ human  -> (id), username, gender, email, password, phone, role, permissions

  ~ article  -> (id), headline, author, date, docpath
  ~ paper -> (id), topic, course, author, license, version, published, docpath
  ~ recent_paper -> (id), topic, course, author, license, version, published, docpath */
  function insertRow($table, $data){  # string (table_name), assoc. array of strings (values)
    try {
      $stmt = self::$conn->prepare($this->sql[$table]);
      $stmt->execute($data);
    } catch (\Exception $e) {
      $this->lastError = "Invalid input parameters. ~ insertRow()";
      error_log("In DbWriter->insertRow(): " . $e->getMessage() . "\n");
      return false;
    }
    return true;
  }

  /*  --- delete() ---
  Safely deletes records fitting condition, doesn't support multiple conditions */
  function delete($table, $column, $value, $operand = "="){  # all strings
    if ($value == "") {  # no conditions, drop all
      $this->lastError = "Dropping everything is not allowed. ~ delete()";
    } else {
      $sql = "DELETE FROM $table WHERE $column $operand ?";
    }
    try {
      $stmt = self::$conn->prepare($sql);
      $stmt->execute([$value]);
    } catch (\Exception $e) {
      error_log("In DbWriter->delete(): " . $e->getMessage() ."\n");
      return false;
    }
    return true;
  }

  /*  --- deleteCustom() ---
  Advanced custom sql deleter not usable with user input */
  function deleteCustom($table, $sqlCondition){
    $sql = "DELETE FROM $table WHERE " . $sqlCondition;
    try {
      $stmt = self::$conn->query($sql);
    } catch (\Exception $e) {
      $this->lastError = "Invalid input parameters. ~ deleteCustom()";
      error_log("In DbWriter->deleteCustom(): " . $e->getMessage() ."\n");
      return false;
    }
    return true;
  }

  /*  --- updateOne() ---
  Modifies one row of database */
  function updateOne($table, $values, $condition){  # string, assoc. array [column => new_value]
    $sql = "UPDATE $table SET ";
    foreach ($values as $column => $value) {
      $sql .= "$column = :$column,";
    }
    $sql = rtrim($sql, ",");  # removing redundant last comma
    $sql .= " WHERE $condition";

    try {
      $stmt = self::$conn->prepare($sql);
      $stmt->execute($values);
    } catch (\Exception $e) {
      error_log("In DbWriter->updateOne(): " . $e->getMessage() . "\n");
      $this->lastError = "Invalid input parameters. ~ updateOne()";
      return false;
    }
    return true;
  }
}
