<?php

/* index.php uses Page class to load custom css, pages, and their widgets' HTML
   from TECHNICALITIES - page
 */
class Page
{
  public $maxRow = 1;  # refers to maxim topleft corner's row, not total
  public $HTML = "";
  public $lastError = "";

  /*  --- sortHTML() ---
  Loads in the $chaoticHTML assoc. array*/
  private function sortHTML($htmlArr){
    $HTML = "";
    // go through rows
    for ($i=1; $i <= $this->maxRow; $i++) {
      if (isset($htmlArr[$i])) {
        $rowArr = array();
        ksort($htmlArr[$i]);
        foreach ($htmlArr[$i] as $column => $widgetHTML) {
          $HTML .= $widgetHTML;
        }
      }
    }
    if ($HTML == "") {
      $this->lastError = "Pages could not be loaded. ~ sortHTML().";
      return false;
    }
    else {
      return $HTML;
    }
  }

  /*  --- getPages() ---
  Loads every page of clone, returns array of page names
  in ascending order by IDs - respects order written in Name, e.g.:
  1Home, 4Contact, 2News, 3About us -> ["Home", "News", "About us", "Contact"]*/
  function getPages()
  {
    $pageList = array();
    $DbReader = new DbReader("TECHNICALITIES");  # connect to DB
    $pages = $DbReader->probe("page", "name");
    unset($DbReader);  # destroy connection
    foreach ($pages as $id => $page) {
      array_push($pageList, substr($page["name"], 1));
    }
    if ($pageList != NULL) {
      return $pageList;
    } else {
      $this->lastError = "Pages could not be loaded. ~ loadPages().";
      return false;
    }
  }

  /* --- loadWidgets() ---
  loads 'widgets' of a given page from DB, converts the string into assoc. array,
  loads widget classes, loads their content into class' string: $HTML and returns it */
  function loadWidgets($page)
  {
    // loading page's 'widgets' array from DB
    $widgets = array();
    $condition = "name LIKE \"%$page\"";  # _ to account for order index
    $DbReader = new DbReader("TECHNICALITIES");  # connect to DB
    $data = $DbReader->probe("page", "widgets", $condition);
    unset($DbReader);  # destroy connection
     if ($data == false) {
      $this->lastError = "Requested page doesn't exist. ~ loadWidgets() using probe()";
      return false;
    }
    $line = array_pop($data);  # stripping the fetch()'ed onion
    $widgetsString = array_pop($line);
    $widgetsChops = explode(", ", $widgetsString);  # conveting string to assoc. array
    foreach ($widgetsChops as $chop) {
      $pair = explode(" => ", $chop);
      $widgets[$pair[0]] = $pair[1];
    }
    if ($widgets == false) {
      $this->lastError = "This page contains no widgets. ~ loadWidgets()";
      return false;
    }

    // loading widgets' classes
    $widget = "";
    $coordinates = "";
    $widgetsHTML = array();  # 2-dimentional array[topleft row][topleft column]
    foreach ($widgets as $classInfo => $content) {
      $classInfo = str_split($classInfo);
      foreach ($classInfo as $i => $char) {  # chopping $classinfo into name and coordinates
        if (in_array($char, range(1, 10))) {
          $classStr = implode($classInfo);
          $coordinates = substr($classStr, $i);
          $widget = rtrim($classStr, $coordinates);
          break;
        }
      }
      $column = $coordinates[0];
      if (!in_array($column, range(1, 10))) {
        $this->lastError = "Scanned 'widgets' array is invalid. ~ loadWidgets()";
        return false;
      }
      $row = substr($coordinates, 1);
      if ($this->maxRow < $row) {
        $this->maxRow = $row;
      }
      if (!isset($widgetsHTML[$row])) {
        $widgetsHTML[$row] = array();
      }

      $class = ucfirst($widget) . "Widget";
      $widgetObj = new $class($column, $row, $content);

      // loading widget's HTML
      try {
        $widgetsHTML[$row][$column] = $widgetObj->HTML();
      } catch (\Exception $e) {
        $this->lastError = "Class $class failed. ~ loadWidgets()";
        return false;
      }
    }
    // sorting widgets to put the into grid in proper order
    $this->HTML = $this->sortHTML($widgetsHTML);
    return $this->HTML;
  }
}
