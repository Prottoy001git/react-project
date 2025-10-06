<?php
// echo "API Working <br>";
require_once('../config/db.php');

// header("Access-Control-Allow-Origin: http://localhost:5173");   // for individual access local or hosting url
header("Access-Control-Allow-Origin: *");  // for universal access
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");  
header("Access-Control-Allow-Headers: Content-Type");  



// require_once('../models/products.class.php');
// require_once('../models/orders.class.php');
// OR
foreach (glob("../models/*.class.php") as $filename) {
    include_once($filename);
}

// include_once('product-api.php');
// include_once('order-api.php');
// OR
foreach(glob("*-api.php") as $filename) {
    include_once($filename);
}
include_once("../helper/img-upload-helper.php");
include_once("../helper/jwt.php");

$request = $_SERVER['REQUEST_METHOD'];

if(isset($_GET['method'])) {
    $endpoint = $_GET['method'];
    // echo $method;
    if($endpoint == 'roles' && $request == 'GET') {
        getRoles();
    }else if($endpoint == 'create-role' && $request == 'POST') {
        // echo "Create role API Working";
        $data = json_decode(file_get_contents("php://input"), true);    //file_get_contents("php://input") is built-in function to read json/raw data,convert json data/raw data into array
        // echo json_encode($data);
        createRole($data);
    }else if($endpoint == 'users' && $request == 'GET') {
        // echo "API Working - Users List";
        getUsers();
    }else if($endpoint == 'create-user' && $request == 'POST') {
        // echo json_encode($_POST);
        // echo json_encode($_FILES);
        createUser($_POST,$_FILES);
    }else if($endpoint == 'delete-user' && $request == 'DELETE') {
        // echo json_encode($_GET['id']);
        deleteUser($_GET['id']);
    }else if($endpoint == 'token' && $request == 'GET') {
        $data = [
            "name" => "Prottoy",
            "email"=> "prottoy@mail.com",
            "role" => "Admin"
        ];
        echo json_encode ("Bearer token: ".generateJWT($data));
    }else{
        echo "This url '$endpoint' not found!";
    }
    
    // if($method == 'products') {
    //     getProducts();
    // }elseif($method == 'product' && isset($_GET['id'])) {
    //     $id = $_GET['id'];
    //     getProductById($id);
    // }elseif($method == 'product-category' && isset($_GET['id'])) {
    //     $id = $_GET['id'];
    //     getProductByCategory($id);
    // }elseif($method == 'orders') {
    //     if(isset($_GET['pg'])) {
    //         $page = $_GET['pg'];
    //         getOrdersByPage($page);
    //     }else {
    //         getOrdersByPage(1);
    //     }
    //     // getOrdersByPage();
    // }
}

?>