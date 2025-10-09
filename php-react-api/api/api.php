<?php
// header("Access-Control-Allow-Origin: http://localhost:5173");   // for individual access local or hosting url
header("Access-Control-Allow-Origin: *");  // for universal access
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// echo "API Working <br>";
require_once('../config/db.php');

// require_once('../models/products.class.php');
// require_once('../models/orders.class.php');
// OR
foreach (glob("../models/*.class.php") as $filename) {
    include_once($filename);
}

// include_once('product-api.php');
// include_once('order-api.php');
// OR
foreach (glob("*-api.php") as $filename) {
    include_once($filename);
}
include_once("../helper/img-upload-helper.php");
include_once("../helper/jwt.php");

if($_SERVER['REQUEST_METHOD'] == 'OPTIONS'){
    http_response_code(200);
    exit();
}

$request = $_SERVER['REQUEST_METHOD'];
$endpoint = $_GET['endpoint'] ?? null;

if (!$endpoint) {
    echo json_encode(["error" => "No endpoint specified"]);
    exit;
}

if ($endpoint == 'login' && $request == 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    // json_encode($data);
    login($data);
} else if ($endpoint == 'token') {
    $data = [
        "name" => "Prottoy",
        "email" => "prottoy@mail.com",
        "role" => "Admin"
    ];
    echo json_encode(generateJWT($data, 60 * 60 * 24 * 7));
} else {
    // $headers = getallheaders();
    // $auth_header = $headers['Authorization'] ?? '';
    // if (!$auth_header) {
    //     http_response_code(401);
    //     echo json_encode(["error" => "No token provided"]);
    //     exit;
    // }
    // $bearer_token = explode(' ', $auth_header);
    // $token = $bearer_token[1];
    // try {
    //     $decoded = validateJWT($token);
    // } catch (Exception $e) {
    //     http_response_code(401);
    //     echo json_encode(["error" => "Invalid or expired token"]);
    //     exit;
    // }

    if (isset($_GET['endpoint'])) {
        $endpoint = $_GET['endpoint'];
        // echo $method;
        if ($endpoint == 'roles' && $request == 'GET') {
            getRoles();
        } else if ($endpoint == 'create-role' && $request == 'POST') {
            // echo "Create role API Working";
            $data = json_decode(file_get_contents("php://input"), true);    //file_get_contents("php://input") is built-in function to read json/raw data,convert json data/raw data into array
            // echo json_encode($data);
            createRole($data);
        } else if ($endpoint == 'users' && $request == 'GET') {
            // echo "API Working - Users List";
            getUsers();
        } else if ($endpoint == 'create-user' && $request == 'POST') {
            // echo json_encode($_POST);
            // echo json_encode($_FILES);
            createUser($_POST, $_FILES);
        } else if ($endpoint == 'delete-user' && $request == 'DELETE') {
            // echo json_encode($_GET['id']);
            deleteUser($_GET['id']);
        } else if ($endpoint == 'token' && $request == 'POST') {
            $data = [
                "name" => "Prottoy",
                "email" => "prottoy@mail.com",
                "role" => "Admin"
            ];
            echo json_encode(generateJWT($data, 60 * 60 * 24 * 7));   // data and duration of validity of token(here 60s)
        } else if ($endpoint == 'token-check') {
            $headers = getallheaders();                     // get all headers data
            $auth_header = $headers['Authorization'] ?? ''; // get if any data in authorization is there
            $jwt = explode(' ', $auth_header);              // split authorization data to 2 elements in an array
            echo json_encode(validateJWT($jwt[1]));                      // get only token using index number from array
        } else {
            echo "This url '$endpoint' not found!";
        }
    }
}
