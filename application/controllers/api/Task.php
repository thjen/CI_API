<?php
  defined('BASEPATH') OR exit('No direct script access allowed');

class Task extends CI_Controller {

  const CONSUMER_SECRET = 'thjenit98';

  public function __construct() {
    parent::__construct();
    //Do your magic here
    $this->load->model('Mtask');
  }
  
  public function create() {
    try {
      $id = Task::getUserId();
      if ($id) {
        $name = $this->input->post('name');
        $description = $this->input->post('description');
        $user_id = $id;
        $data = array(
          "name" => $name,
          "description" => $description,
          "user_id" => $user_id
        );
        if ($this->Mtask->insert($data)) {
          echo json_encode(array("code" => -1, "message" => "Task has been created"));
        } else {
          echo json_encode(array("code" => -1, "message" => "Error"));
        }
      } else {
        echo json_encode(array("code" => -1, "message" => "You have not sign in"));
      }
    } catch (\Exception $e) {
      echo json_encode(array("code" => -1, "message" => "Create has been errored"));
    }
  }

  public function fetch() {
    try {
      $id = Task::getUserId();
      if ($id) {
        $task = $this->Mtask->fetchTaskById($id);
        if ($task) {
          echo $task;
        } else {
          echo json_encode(array("code" => -1, "message" => "Fetching error"));
        }
      } else {
        echo json_encode(array("code" => -1, "message" => "Fetching error"));
      }
    } catch (\Exception $e) {
      echo json_encode(array("code" => -1, "message" => "Fetching error"));
    }
  }

  public function update() {
    try {
      $userId = Task::getUserId();
      if ($userId) {
        $taskId = $this->input->post('taskId');
        $name = $this->input->post('name');
        $description = $this->input->post('description');
        $task = array(
          "name" => $name,
          "description" => $description,
          "user_id" => $userId
        );
        if ($this->Mtask->updateTaskById($userId, $taskId, $task)) {
          echo json_encode(array("code" => -1, "message" => "Update task successfully"));
        } else {
          echo json_encode(array("code" => -1, "message" => "Task is not exist"));
        }
      } else {
        echo json_encode(array("code" => -1, "message" => "Update task has been errored"));
      }
    } catch (\Exception $e) {
      echo json_encode(array("code" => -1, "message" => "Update task has been errored"));
    }
  }

  public function get() {
    try {
      $id = Task::getUserId();
      if ($id) {
        $taskId = $this->input->post('taskId');
        $task = $this->Mtask->getTaskById($id, $taskId);
        if ($task) {
          echo $task;
        } else {
          echo json_encode(array("code" => -1, "message" => "Task is not exist"));
        }
      } else {
        echo json_encode(array("code" => -1, "message" => "Get task has been errored"));
      }
    } catch (\Exception $e) {
      echo json_encode(array("code" => -1, "message" => "Get task has been errored"));
    }
  }

  public function delete() {
    try {
      $userId = Task::getUserId();
      if ($userId) {
        $taskId = $this->input->post('taskId');
        if ($this->Mtask->deleteTaskById($userId, $taskId)) {
          echo json_encode(array("code" => -1, "message" => "Delete task successfully"));
        } else {
          echo json_encode(array("code" => -1, "message" => "Delete task has been errored"));
        }
      } else {
        echo json_encode(array("code" => -1, "message" => "Delete task has been errored"));
      }
    } catch (\Exception $e) {
      echo json_encode(array("code" => -1, "message" => "Delete task has been errored"));
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

  public function getUserId() {
    try {
      if ($this->session->userdata('token')) {
        $email = Task::decode($this->session->userdata('token'))->userEmail;
        return $this->Mtask->getId($email);
      } else {
        return 0;
      }
    } catch (\Exception $e) {
      return 0;
    }
  }

}