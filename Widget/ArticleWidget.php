<!--
So far used:
quote (span 3 span 1)
picture (span 4 span 3)
table (span 3 span 3)
article (span 5 span INF)
-->
<?php
/* A piece of text and its author, one day perhaps with a picture

All content loaded from TECHNICALITIES - page - widgets */

class ArticleWidget implements PassiveWidget
{
  function __construct($column, $row, $widgetString){
    $this->column = $column;  #  determines position in the GRID
    $this->row = $row;  #  determines position in the GRID
    $this->widgetString = $widgetString;  # verbatim from DB
  }
}
