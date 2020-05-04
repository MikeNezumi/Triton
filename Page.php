<?php

/* index.php uses Page class to load custom css, pages, and their widgets' HTML
   from TECHNICALITIES - page
 */
class Page extends DbReader
{
  public $HTML = array();

  /* File classes run exclusively for TECHNICALITIES db */
  function __construct()
  {
    parent::__construct("TECHNICALITIES");
  }

  /*  --- loadPages() ---
  Loads every page of clone, returns array of page names
  in ascending order by IDs - respects order written in Name, e.g.:
  1Home, 4Contact, 2News, 3About us -> ["Home", "News", "About us", "Contact"]*/
  function loadPages()
  {
    $pageList = array();
    $pages = $this->probe("page", "name");
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

  /*  --- readWidget() ---
  reads 'widgets' column of the given page, loads its corresponding class
  and adds it to $HTML*/
  function readWidgets($page)
  {
    // probe 'page' for 'widgets', load classes and roll!
  }
}
