<?php
  defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {
   
  const CONSUMER_KEY = 'thjenit98';
  const CONSUMER_SECRET = 'thjenit98';
  const CONSUMER_TTL = 86400;

  public function __construct() {
    parent::__construct();
    //Do your magic here
    $this->load->model('Muser');
  }
  
  public function login() {
    header('Content-Type: application/json');
    try {
      $email = $this->input->post('email');
      $password = $this->input->post('password');
      if (!empty($email) && !empty($password)) {
        if (Validation::emailIsValid($email)) {
          if ($this->Muser->checkLogin($email, $password)) {      
            $jwt = User::create($email);     
            $this->session->set_userdata('token', $jwt);
            echo json_encode(array("code" => 1, "message" => "Login successfully", "token" => $jwt));
          } else {
            echo json_encode(array("code" => -1, "message" => "Login failed"));
          }
        } else {
          echo "Email is not valid";
        }
      } else {
        echo json_encode(array("code" => -1, "message" => "You have not enter email and password"));
      }
    } catch (\Exception $e) {
      echo json_encode(array("status" => -1, "message" => "Login error"));
    } 
  }

  public function register() {
    header('Content-Type: application/json');
    try {
      $firstname = $this->input->post("firstname");
      $lastname = $this->input->post("lastname");
      $email = $this->input->post('email');
      $phone = $this->input->post('phone');
      $password = md5($this->input->post("password"));
      if (!empty($firstname) && !empty($phone) && !empty($lastname) && !empty($email) && !empty($password)) {
        if (Validation::emailIsValid($email)) {
          $user = array(
            "firstname" => $firstname,
            "lastname" => $lastname,
            "email" => $email,
            "phone_number"=>$phone,
            "password" => $password,
            "role" => 1,
          );
          if ($this->Muser->userRegister($user)) {
            echo json_encode(array("status" => 1, "message" => "Register successfully"));
          } else {
            echo json_encode(array("status" => -1, "message" => "Register Error"));
          }
        } else {
          echo "Email is not valid";
        }
      } else {
        echo json_encode(array("status" => -1, "message" => "You have not fullfill form data"));
      }
    } catch (\Exception $e) {
      echo json_encode(array("status" => -1, "message" => "Register Error"));
    }
  }

  public function changePassword() {
    header('Content-Type: application/json');
    $email = trim($this->input->post('email'));
    $newpass = trim($this->input->post('newpass'));
    $oldpass = trim($this->input->post('oldpass'));
    if (!empty($email) && !empty($newpass) && !empty($oldpass)) {
      if (Validation::emailIsValid($email)) {
        if ($this->Muser->updatePassword($email, $oldpass, $newpass)) {
          echo json_encode(array("status" => -1, "message" => "Change password successfully"));
        } else {
          echo json_encode(array("status" => -1, "message" => "Error"));
        }
      } else {
        echo "Email is not valid";
      }
    } else {
      echo json_encode(array("status" => -1, "message" => "You have not fullfill form data"));
    }
  }

  public function forgotPassword() {
    header('Content-Type: application/json');
    $email = trim($this->input->post('email'));
    if (!empty($email)) {
      if (Validation::emailIsValid($email)) {
        $email = strtolower($email); 
        /*$config = Array(
          'protocol' => 'smtp',
          'smtp_host' => 'ssl://smtp.googlemail.com',
          'smtp_port' => 465,
          'smtp_user' => 'thjenit98@gmail.com',
          'smtp_pass' => '096851159701658247153t',
          'mailtype'  => 'html', 
          'charset'   => 'iso-8859-1'
        );*/
        $this->load->library('email');
        //$this->email->set_newline("\r\n");
        $userPass = bin2hex(mcrypt_create_iv(5, MCRYPT_DEV_RANDOM));
        $userPass = substr($userPass, 0, 14);
        $subject = "Your password";
        $from = 'thjenit98@gmail.com';
        $this->email->from($from, "Thiện Quốc");
        $this->email->to($email);
        $this->email->subject($subject);
        $this->email->message($userPass);
        if ($this->email->send()) {    
          $oldpass = $this->Muser->getPass($email);
          $this->Muser->updatePassword($email, $oldpass, $userPass);
        } else {
          echo json_encode(array("status" => -1, "message" => "Send has been error"));
        }
      } else {
        echo json_encode(array("status" => -1, "message" => "Email is not valid!"));
      }
    } else {
      echo json_encode(array("status" => -1, "message" => "Email is not empty!"));
    }
  }

  public function create($userEmail) {
    $token = JWT::encode(array(
        'consumerKey' => self::CONSUMER_KEY,
        'userEmail' => $userEmail,
        'issuedAt' => date(DATE_ISO8601, strtotime("now")),
        'ttl' => self::CONSUMER_TTL
    ), self::CONSUMER_SECRET);
    return $token;
  }

  public function validate($token) {
    try {
      $decodeToken = JWT::decode($token, self::CONSUMER_SECRET);
      // validate token is not expired
      $ttl_time = strtotime($decodeToken->issuedAt);
      $now_time = strtotime(date(DATE_ISO8601, strtotime("now")));
      if(($now_time - $ttl_time) > $decodeToken->ttl) {
        throw new Exception('Expired');
      } else {
        return true;
      }
    } catch (Exception $e) {
      return false;
    }
  }

  public function decode($token) {
    try {
      $decodeToken = JWT::decode($token, self::CONSUMER_SECRET);
      return $decodeToken;
    } catch (Exception $e) {
      return false;
    }
  }

  public function logout() {
    try {
      unset($_SESSION['token']);
      echo json_encode(array("status" => -1, "message" => "Logout successfully"));
    } catch (\Exception $e) {
      echo json_encode(array("status" => -1, "message" => "Error"));
    }
  }

  public function checkToken() {
    echo $this->session->userdata('token');
  }

}
