<?php

class MapChanges
{
  private $x;
  private $y;
  private $field;
  private $from;
  private $to;
  function __construct($x, $y, $field, $from, $to) {
    $this->x = $x;
    $this->y = $y;
    $this->field = $field;
    $this->from = $from;
    $this->to = $to;
  }

  function apply() {
    global $db;
    $sql = "update map set $this->field = ".
      "if ( $this->field = $this->from, $this->to, $this->from ), ".
      "info = floor(decor/100) where x = $this->x and y = $this->y";
    $db->query($sql);
  }
}

