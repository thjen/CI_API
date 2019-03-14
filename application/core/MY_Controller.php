<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

  public function __construct() {
    parent::__construct();
    //Do your magic here
  }
  
  protected function loadModel($models = array()){
    foreach ($models as $model) $this->load->model($model);
  }

  protected function arrayFromPost($fields){
      $data = array();
      foreach ($fields as $field) $data[$field] = trim($this->input->post($field));
      return $data;
  }
}