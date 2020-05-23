<?php
/* A piece of text and its author, one day perhaps with a picture

Content loaded from TECHNICALITIES and IMAGE - page - widgets */

class PictureWidget implements PassiveWidget
{
  function __construct($column, $row, $widgetString){
    $this->column = $column;  #  determines position in the GRID
    $this->row = $row;  #  determines position in the GRID
    $this->widgetString = $widgetString;  # verbatim from DB
  }
/*   --- HTML() ---
When called, composes and returnes its HTML content,
including CSS for positioninig itself within GRID
$widgetString format for Picture: $docpath | ($aboveCaption) | ($belowCaption)
*/
  function HTML(){
    $picHTML = "";  # html grid building block
    $picContent = "";
    $picDocpath = "";
    $elements = ["", "", ""];
    $content = explode(" | ", $this->widgetString);
    foreach ($content as $key => $value) {
      if ($key > 2) {  # simplest overload prevention
        break;
      }
      $elements[$key] = $value;
    }
    try {
      $DbReader = new DbReader("MEDIA");
      $record = $DbReader->probe("image", "*", "docpath = \"$elements[0]\"");
      $picContent = $record[0]["content"];
      $picDocpath = $record[0]["docpath"];
    } catch (\Exception $e) {
      $picHTML = "Loading image went wrong )-:";
      error_log("In PictureWidget: DbReader->probe(): " . $e->getMessage() . "\n");
    } finally {
      if (empty($record)) {
        $picHTML = "Image not found )-:";
      } else {
        $picHTML = "<img src=\"$picDocpath\" alt=\"$picContent\">";
      }

      $gridCSS = "
        grid-column: $this->column span 4;
        grid-row: $this->row span 3;
      ";

      $html = "
        <div class=\"picture\" style=\"$gridCSS\">
          $picHTML
        </div>
      ";

      return $html;
    }
  }
}
