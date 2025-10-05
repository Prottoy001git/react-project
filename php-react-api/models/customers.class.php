<?php

class Customers {
    public $id;
    public $name;
    public $email;
    public $phone;
    public $address;

    public function __construct($_id, $_name, $_email, $_phone, $_address) {
        $this->id = $_id;
        $this->name = $_name;
        $this->email = $_email;
        $this->phone = $_phone;
        $this->address = $_address;
    }

    public function create() {
        global $db;
        $sql = "INSERT INTO ecom_customers (id,name,email,phone,address) VALUES ('{$this->id}', '{$this->name}', '{$this->email}', '{$this->phone}', '{$this->address}')";
        if ($db->query($sql)) {
          return $db->insert_id;
        } else {
          return "Query failed: " . $db->error;
        }
    }

    public static function readAll() {
        global $db;
        $sql = "SELECT * FROM ecom_customers";
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
        $sql = "SELECT * FROM ecom_customers WHERE id = $id";
        $res = $db->query($sql);
        if ($res) {
          return $res->fetch_assoc();
        } else {
          return "Query failed: " . $db->error;
        }
    }

    public function update($id) {
        global $db;
        $sql = "UPDATE ecom_customers SET id='{$this->id}', name='{$this->name}', email='{$this->email}', phone='{$this->phone}', address='{$this->address}' WHERE id = $id";
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
        $sql = "DELETE FROM ecom_customers WHERE id = $id";
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
