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

class QuoteWidget implements PassiveWidget
{
  function __construct($column, $row, $widgetString){
    $this->column = $column;  #  determines position in the GRID
    $this->row = $row;  #  determines position in the GRID
    $this->widgetString = $widgetString;  # verbatim from DB
  }

  /*   --- HTML() ---
  When called, composes and returnes its HTML content,
  including CSS for positioninig itself within GRID
  */
  function HTML(){
    $quote = "";
    $author = "";
    $text = explode(" - ", $this->widgetString);
    if (sizeof($text) > 1) {
      $author = $text[1];
    }
    $quote = $text[0];

    $gridCSS = "
      grid-column: $this->column span 3;
      grid-row: $this->row span 1;

      font-size: 15px;
      text-align: right;
    ";
    $pCSS = "
      text-align: center;
      font-style: italic;
      font-size: 20px;
    ";

    $html = "
      <div class=\"quote\" style=\"$gridCSS\">
        <p style = \"$pCSS\">$quote</p><br>
          - $author
      </div>
    ";
    return $html;
  }
}
