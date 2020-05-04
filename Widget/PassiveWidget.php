<?php
/* 'passive widget' is a non-interactive widget that loads data and sends finished HTML.
There is no input from the user. It's a painting, not a tractor.

All passive widgets have identical constructors and method HTML(); */
interface PassiveWidget
{
  function __construct($column, $row, $widgetString);  # same for all widgets
  function HTML();
}
