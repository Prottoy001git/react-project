<?php

class OrderStatus {
    public $id;
    public $name;

    public function __construct($_id, $_name) {
        $this->id = $_id;
        $this->name = $_name;
    }

    public function create() {
        global $db;
        $sql = "INSERT INTO order_status (id,name) VALUES ('{$this->id}', '{$this->name}')";
        if ($db->query($sql)) {
          return $db->insert_id;
        } else {
          return "Query failed: " . $db->error;
        }
    }

    public static function readAll() {
        global $db;
        $sql = "SELECT * FROM order_status";
        $res = $db->query($sql);
        if ($res) {
          return $res->fetch_all(MYSQLI_ASSOC);
        } else {
          return "Query failed: " . $db->error;
        }
    }

    public static function readById($id) {
        global $db;
        $id = (int)$id;
        $sql = "SELECT * FROM order_status WHERE id = $id";
        $res = $db->query($sql);
        if ($res) {
          return $res->fetch_assoc();
        } else {
          return "Query failed: " . $db->error;
        }
    }

    public function update($id) {
        global $db;
        $sql = "UPDATE order_status SET id='{$this->id}', name='{$this->name}' WHERE id = $id";
        if ($db->query($sql)) {
          if ($db->affected_rows > 0) {
            return "Update successful.";
          } else {
            return "No changes made or record not found.";
          }
        } else {
          return "Update failed: " . $db->error;
        }
    }

    public static function delete($id) {
        global $db;
        $sql = "DELETE FROM order_status WHERE id = $id";
        if ($db->query($sql)) {
          if ($db->affected_rows > 0) {
            return "Delete successful.";
          } else {
            return "No record found with ID $id.";
          }
        } else {
          return "Delete failed: " . $db->error;
        }
    }
}
