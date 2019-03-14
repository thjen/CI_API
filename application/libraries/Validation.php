<?php
  defined('BASEPATH') OR exit('No direct script access allowed');

class Validation {
  
  public static function emailIsValid($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
  }

}