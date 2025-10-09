<?php

class Users {
    public $id;
    public $name;
    public $email;
    public $password;
    public $role_id;
    public $address;
    public $photo;

    public function __construct($_id, $_name, $_email, $_password, $_role_id, $_address, $_photo) {
        $this->id = $_id;
        $this->name = $_name;
        $this->email = $_email;
        $this->password = $_password;
        $this->role_id = $_role_id;
        $this->address = $_address;
        $this->photo = $_photo;
    }

    public function create() {
        global $db;
        $sql = "INSERT INTO ecom_users (id,name,email,password,role_id,address,photo) VALUES ('{$this->id}', '{$this->name}', '{$this->email}', '{$this->password}', '{$this->role_id}', '{$this->address}', '{$this->photo}')";
        if ($db->query($sql)) {
          return $db->insert_id;
        } else {
          return "Query failed: " . $db->error;
        }
    }

    public static function readAll() {
        global $db;
        $sql = "SELECT u.id, u.name, u.email, u.address, u.photo,r.name AS role FROM ecom_users u, ecom_roles r WHERE u.role_id = r.id";
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
        $sql = "SELECT * FROM ecom_users WHERE id = $id";
        $res = $db->query($sql);
        if ($res) {
          return $res->fetch_assoc();
        } else {
          return "Query failed: " . $db->error;
        }
    }

    public function update($id) {
        global $db;
        $sql = "UPDATE ecom_users SET id='{$this->id}', name='{$this->name}', email='{$this->email}', password='{$this->password}', role_id='{$this->role_id}', address='{$this->address}', photo='{$this->photo}' WHERE id = $id";
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
        $sql = "DELETE FROM ecom_users WHERE id = $id";
        if ($db->query($sql)) {
          if ($db->affected_rows > 0) {
            return "Id $id Delete successful.";
          } else {
            return "No record found with ID $id.";
          }
        } else {
          return "Delete failed: " . $db->error;
        }
    }
    public static function login($_email, $_password) {
        global $db;
        $sql ="SELECT * from ecom_users WHERE email = '{$_email}' AND password = '$_password'";

        $res = $db->query($sql);
        if($res){
          if($res->num_rows > 0){
            return ["success" => "Login successful", "user_data" => $res->fetch_assoc()];
          }else{
            return ["error" => "Invalid email or password"];
          }
          return $res->fetch_assoc();
        }else{
          return "Query failed: " . $db->error;
        }
    }
}
