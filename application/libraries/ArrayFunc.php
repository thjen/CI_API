<?php
  defined('BASEPATH') OR exit('No direct script access allowed');

class ArrayFunc {
  public static function indexOf($checker, $array) {
    $i = 0;
    $temp = 0;
    while ($i < count($array)) {
      if ($array[$i]["id"] == $checker) {
        $temp = 1;
        break;
      } else {
        $temp = 0;
      }
      $i++;
    } 
    if ($temp) {
      return 1;
    } else {
      return 0;
    }
  }
}