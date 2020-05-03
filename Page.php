<?php

/* index.php uses Page class to load custom css, pages, and their widgets' HTML
   from TECHNICALITIES - page
 */
class Page extends DbReader
{
  public $HTML = array();

  /* File classes run exclusively for DOCUMENTS db */
  function __construct()
  {
    parent::__construct("TECHNICALITIES");
  }

  function loadPages()
  {
    $pages = $this->probe("page", "page");
    print_r($pages);
  }
}
