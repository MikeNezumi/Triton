<?php
require_once('Db.php');  # temporary (autoloading from index.php in distribution)
/* Prober for the purpose of reading tables*/
class DbReader extends Db
{
  function __construct($server, $user, $password, $database)
  {
    $this->server = $server;
    $this->user = $user;
    $this->password = $password;
    $this->database = $database;
    self::connect($server, $user, $password, $database);
  }

  var $lastError = "No recorded errors.";  # last error that occured using this object

  /*   --- probe() ---
  Read data from 1 table,
  returs assoc. array of arrays
  id => row:
    columns */
  function probe($table, $columns = "*", $conditions = "true"){  # sql strings
    try {
      $content = self::$conn->query("SELECT $columns FROM $table WHERE $conditions;");
      return $content->fetchAll();
    } catch (\Exception $e) {
      $this->lastError = "Invalid input parameters. ~ probe()";
      error_log("In DbReader->probe(): " . $e->getMessage() . "\n");
      return false;
    }
  }

  /* --- probeAll() ---
  Read data from all tables in DB,  # ERRORS NOT HANDELED YET!
  returs assoc. array of asoc. arrays of arrays:
  table name => rows:
    id => row:
      columns */
  function probeAll($columns = "*", $conditions = "true")
  {
    $content = array();
    try {
      $tables = self::$conn->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
      foreach ($tables as $table_name) {
        $one_table = $this->probe($table_name, $columns, $conditions);
        $content[$table_name] = $one_table;
      }
      return $content;
    } catch (\Exception $e) {
      $this->lastError = "Invalid input parameters. ~ probeAll()";
      error_log("In DbReader->probeAll(): " . $e->getMessage() . "\n");
      return false;
    }
  }
}
