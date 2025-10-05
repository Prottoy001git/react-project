<?php

class OrderDetails {
    public $id;
    public $order_id;
    public $product_id;
    public $price;
    public $qty;

    public function __construct($_id, $_order_id, $_product_id, $_price, $_qty) {
        $this->id = $_id;
        $this->order_id = $_order_id;
        $this->product_id = $_product_id;
        $this->price = $_price;
        $this->qty = $_qty;
    }

    public function create() {
        global $db;
        $sql = "INSERT INTO ecom_order_details (id,order_id,product_id,price,qty) VALUES ('{$this->id}', '{$this->order_id}', '{$this->product_id}', '{$this->price}', '{$this->qty}')";
        if ($db->query($sql)) {
          return $db->insert_id;
        } else {
          return "Query failed: " . $db->error;
        }
    }

    public static function readAll() {
        global $db;
        $sql = "SELECT * FROM ecom_order_details";
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
        $sql = "SELECT * FROM ecom_order_details WHERE id = $id";
        $res = $db->query($sql);
        if ($res) {
          return $res->fetch_assoc();
        } else {
          return "Query failed: " . $db->error;
        }
    }

    public function update($id) {
        global $db;
        $sql = "UPDATE ecom_order_details SET id='{$this->id}', order_id='{$this->order_id}', product_id='{$this->product_id}', price='{$this->price}', qty='{$this->qty}' WHERE id = $id";
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
        $sql = "DELETE FROM ecom_order_details WHERE id = $id";
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
