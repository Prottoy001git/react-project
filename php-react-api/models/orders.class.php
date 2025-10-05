<?php
// require_once("models/order-details.class.php");
class Orders {
    public $id;
    public $customer_id;
    public $total_amount;
    public $shipping_address;
    public $order_status_id;

    public function __construct($_id, $_customer_id, $_total_amount, $_shipping_address, $_order_status_id) {
        $this->id = $_id;
        $this->customer_id = $_customer_id;
        $this->total_amount = $_total_amount;
        $this->shipping_address = $_shipping_address;
        $this->order_status_id = $_order_status_id;
    }

    public function create() {
        global $db;
        $sql = "INSERT INTO ecom_orders (customer_id,total_amount,shipping_address,order_status_id) VALUES ( '{$this->customer_id}', '{$this->total_amount}', '{$this->shipping_address}', '{$this->order_status_id}')";
        if ($db->query($sql)) {
          // return $db->insert_id;
          return "Data saved successfully";
        } else {
          return "Query failed: " . $db->error;
        }
    }
    public function createOrder($_items) {
        global $db;
        $sql = "INSERT INTO ecom_orders (customer_id,total_amount,shipping_address,order_status_id) VALUES ( '{$this->customer_id}', '{$this->total_amount}', '{$this->shipping_address}', '{$this->order_status_id}')";
        if ($db->query($sql)) {
          $order_id = $db->insert_id;
        }else{
          return "Query failed: " . $db->error;
        }
        // print_r($_items);
        foreach($_items as $item) {
          $order_details = new OrderDetails(null, $order_id, $item->id, ($item->price - $item->discount), $item->quantity);
          $res = $order_details->create();

          if(is_int($res)) {
            $result = "Data saved successfully";
          }else{
            return $res;
          }
        }
        return $result;
    }

    public static function readAll($_skip = 0, $_limit = 5) {
        global $db;
        $sql = "SELECT o.*, os.name 
                FROM ecom_orders o, order_status os 
                WHERE o.order_status_id = os.id 
                ORDER BY o.id DESC
                LIMIT $_limit OFFSET $_skip
                ";
                // LIMIT 5 OFFSET 2
        $res = $db->query($sql);
        if ($res) {
          return $res->fetch_all(MYSQLI_ASSOC);
        } else {
          return "Query failed: " . $db->error;
        }
    }
    public static function readAllForCustomer($_customer_id) {
        global $db;
        $sql = "SELECT o.*, os.name 
                FROM ecom_orders o, order_status os 
                WHERE o.order_status_id = os.id AND o.customer_id = $_customer_id
                ORDER BY o.id DESC";
                // LIMIT 5 OFFSET 2
        $res = $db->query($sql);
        if ($res) {
          return $res->fetch_all(MYSQLI_ASSOC);
        } else {
          return "Query failed: " . $db->error;
        }
    }
    public static function orderRowsNo() {
        global $db;
        $sql = "SELECT COUNT(*) as orders_count FROM ecom_orders";
        $res = $db->query($sql);
        if ($res) {
          return $res->fetch_assoc();
        } else {
          return $db->error;
        }
    }

    public static function readByPage($_page = 1, $_limit = 5) {
        global $db;
        $skip = ($_page - 1) * $_limit;
        $sql = "SELECT o.*, os.name status
                FROM ecom_orders o, order_status os 
                WHERE o.order_status_id = os.id 
                ORDER BY o.id DESC
                LIMIT $_limit OFFSET $skip
                -- pg:1 skip:0
                -- pg:2 skip:5
                -- pg:3 skip:10
                ";
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
        $sql = "SELECT * FROM ecom_orders WHERE id = $id";
        $res = $db->query($sql);
        if ($res) {
          return $res->fetch_assoc();
        } else {
          return "Query failed: " . $db->error;
        }
    }

    public function update($id) {
        global $db;
        $sql = "UPDATE ecom_orders SET id='{$this->id}', customer_id='{$this->customer_id}', total_amount='{$this->total_amount}', shipping_address='{$this->shipping_address}', date='{$this->date}', order_status_id='{$this->order_status_id}' WHERE id = $id";
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
        $sql = "DELETE FROM ecom_orders WHERE id = $id";
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
