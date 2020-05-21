<!--
So far used:
quote (span 3 span 1)
picture (span 4 span 3)
table (span 3 span 3)
article (span 5 span INF)
-->
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
    $picHTML = "";  # html building block
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
    } catch (\Exception $e) {
      $picHTML = "Loading image went wrong )-:";
      error_log("In PictureWidget: DbReader->probe(): " . $e->getMessage() . "\n");
    } finally {
      if (empty($record)) {
        echo  "First element: II" . $elements[0] . "II<br>";
        echo "Last error: " . $DbReader->lastError;
        $picHTML = "Image not found )-:";
      } else {
        $picHTML = "<img src=\"$picDocpath\" alt=\"$picContent\">";
      }
      echo "<br>Submarine!<br>";
      print_r($record);
    }
  }
}
