<?php
  defined('BASEPATH') OR exit('No direct script access allowed');

class Muser extends CI_Model {

  public function userRegister($user) {
    try {
      $this->db->insert("user", $user);
      return $this->db->insert_id();
    } catch (\Exception $e) {
      return 0;
    }
  }

  public function userIsExist($email) {
    try {
      $query = "SELECT COUNT(email) AS 'result' FROM user WHERE email = ?";
      $query = $this->db->query($query, array($email));
      $result = $query->result_array();
      return $result[0]['result'];
    } catch (\Exception $e) {
      return 0;
    }
  }

  public function checkLogin($email, $password) {
    try {
      if (Muser::userIsExist($email)) {
        $query = "SELECT password FROM user WHERE email = ?";
        $query = $this->db->query($query, array($email));
        $result = $query->result_array();
        if (md5($password) == $result[0]['password']) {
          return 1;
        } else {
          return 0;
        }
      } else {
        echo json_encode(array("code" => 201, "message" => "Email is not exist"));
      }
    } catch (\Exception $e) {
      return 0;
    }
  }

  public function getPass($email) {
    try {
      $this->db->select('password');
      $this->db->where('email', $email);
      $pass = $this->db->get('user');
      $pass = $pass->result_array();
      return $pass[0]['password'];
    } catch(\Exception $e) {
      return 0;
    }
  }

  public function updatePassword($email, $oldpass, $newpass) {
    try {
      if (Muser::checkLogin($email, $oldpass)) {
        $this->db->set('password', md5($newpass));
        $this->db->where('email', $email);
        if ($this->db->update('user')) {
          return 1;
        } else { 
          return 0;
        }
      } else {
        return 0;
      }
    } catch (\Exception $e) {
      return 0;
    }
  }

}