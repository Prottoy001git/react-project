<?php

class Products {
    public $id;
    public $name;
    public $category_id;
    public $price;
    public $discount;
    public $quantity;
    public $reorder_point;
    public $photo;
    public $is_active;

    public function __construct($_id, $_name, $_category_id, $_price, $_discount, $_quantity, $_reorder_point, $_photo, $_is_active) {
      $this->name = $_name;
        $this->id = $_id;
        $this->category_id = $_category_id;
        $this->price = $_price;
        $this->discount = $_discount;
        $this->quantity = $_quantity;
        $this->reorder_point = $_reorder_point;
        $this->photo = $_photo;
        $this->is_active = $_is_active;
    }

    public function create() {
        global $db;
        $sql = "INSERT INTO ecom_products (id,name,category_id,price,discount,quantity,reorder_point,photo,is_active) VALUES ('{$this->id}', '{$this->name}', '{$this->category_id}', '{$this->price}', '{$this->discount}', '{$this->quantity}', '{$this->reorder_point}', '{$this->photo}', '{$this->is_active}')";
        if ($db->query($sql)) {
          // return $db->insert_id;
          return "Data saved successfully!";
        } else {
          return "Query failed: " . $db->error;
        }
    }

    public static function readAll() {
        global $db;
        $sql = "SELECT p.*,c.name as category FROM ecom_products p, ecom_categories c WHERE p.category_id = c.id";
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
        $sql = "SELECT * FROM ecom_products WHERE id = $id";
        $res = $db->query($sql);
        if ($res) {
          return $res->fetch_assoc();
        } else {
          return "Query failed: " . $db->error;
        }
    }

    public function update($id) {
        global $db;
        $sql = "UPDATE ecom_products SET id='{$this->id}', name='{$this->name}', category_id='{$this->category_id}', price='{$this->price}', discount='{$this->discount}', quantity='{$this->quantity}', reorder_point='{$this->reorder_point}', photo='{$this->photo}', is_active='{$this->is_active}' WHERE id = $id";
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
        $sql = "DELETE FROM ecom_products WHERE id = $id";
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

    public static function readByCategory($_id) {
       global $db;
       $sql = "SELECT p.*,c.name as category FROM ecom_products p, ecom_categories c WHERE p.category_id = c.id AND p.category_id = $_id";
       $res = $db->query($sql);
       if ($res) {
         return $res->fetch_all(MYSQLI_ASSOC);
       } else {
         return "Query failed: " . $db->error;
       }
    }
}
