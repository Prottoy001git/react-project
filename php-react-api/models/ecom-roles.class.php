<?php
// require_once("config/db.php");
class EcomRoles {
    public $id;
    public $name;
    public function __construct($_id, $_name) {
        $this->id = $_id;
        $this->name = $_name;
    }
    public static function readAll(){
        global $db;
        $result = $db->query("select * from ecom_roles");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    public static function readById($_id){
        global $db;
        $result = $db->query("select * from ecom_roles where id = $_id");
        return $result->fetch_assoc();
    }
    public function create(){
        global $db;
        $result = $db->query("insert into ecom_roles(name) values('$this->name')");
        if($result) {
            return "Data saved successfully!";
            // return $db->insert_id;
        }else{
            return $db->error;
        }
    }
    public function update(){
        global $db;
        $result = $db->query("update ecom_roles set name='$this->name' where id=$this->id");
        if($result) {
            return "Data updated successfully!";
        }else{
            return $db->error;
        }
    }
    public static function delete($_id){
        global $db;
        $result = $db->query("delete from ecom_roles where id = $_id");
        if($result) {
            return "Data deleted successfully!";
        }else{
            return $db->error;
        }
    }

    // public static function test() {
    //     global $db;
    //     $result = $db->query("select * from ecom_users");
    //     print_r($result);
    // }
}
?>