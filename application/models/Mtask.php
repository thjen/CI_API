<?php
  defined('BASEPATH') OR exit('No direct script access allowed');

class Mtask extends CI_Model {

  public function getId($email) {
    try {
      $this->db->select('id');
      $this->db->where('email', $email);
      $id = $this->db->get('user');
      $id = $id->result_array();
      return $id[0]['id'];
    } catch (\Exception $e) {
      return 0;
    }
  }

  public function insert($data) {
    try {
      $this->db->insert('task', $data);
      return $this->db->insert_id();
    } catch (\Exception $e) {
      return 0;
    }
  }

  public function fetchTaskById($id) {
    try {
      $query = "
        SELECT t.*
        FROM task as t JOIN user as u ON t.user_id = u.id
        WHERE u.id = ?";
      $tasks = $this->db->query($query, array($id));
      $tasks = $tasks->result_array();
      $tasks = array(
        "code" => -1,
        "tasks" => $tasks
      );
      return json_encode($tasks);
    } catch (\Exception $e) {
      return 0;
    }
  }

  public function getTaskById($userId, $taskId) {
    try {
      if (Mtask::checkTaskOfUser($userId, $taskId)) {
        $query = "SELECT * FROM task WHERE id = ?";
        $task = $this->db->query($query, array($taskId));
        $task = $task->result_array();
        $task = array(
          "code" => -1,
          "task" => $task
        );
        return json_encode($task);
      } else {
        return 0;
      }
    } catch (\Exception $e) {
      return 0;
    }
  }

  public function updateTaskById($userId, $taskId, $task) {
    try {
      if (Mtask::checkTaskOfUser($userId, $taskId)) {
        $this->db->where('id', $taskId);
        if ($this->db->update('task', $task)) {
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

  public function deleteTaskById($userId, $taskId) {
    try {
      if (Mtask::checkTaskOfUser($userId, $taskId)) {
        $this->db->where("id", $taskId);
        if ($this->db->delete("task")) {
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

  /**TODO: check id of task list task of user */
  public function checkTaskOfUser($userId, $taskId) {
    try {
      $userGetTaskId = "
        SELECT t.id
        FROM task as t JOIN user as u ON t.user_id = u.id
        WHERE u.id = ?";
      $data = $this->db->query($userGetTaskId, array($userId));
      $data = $data->result_array();
      $this->load->library("ArrayFunc");
      if (ArrayFunc::indexOf($taskId, $data)) {
        return 1;
      } else {
        return 0;
      }
    } catch (\Exception $e) {
      return 0;
    }
  }

}