<?php
require_once "../vendor/autoload.php";
use Firebase\JWT\JWT;   //built in class to create jwt token
use Firebase\JWT\key;   //

$config = include('../config/secret.php');

function generateJWT($payload, $expiry = 3600) {    //this function generates jwt token
    global $config;
    $issuedAt = time();                             //sets current time in unix format for jwt
    $expire = $issuedAt + $expiry;                  // Token valid for 1 hour
    $tokenPayload = array_merge($payload, [
        'iat' => $issuedAt,
        'exp' => $expire
    ]);
    return JWT::encode($tokenPayload, $config['secret_key_jwt'], 'HS256');  // use the secret file array key, the value will be applied
}

function validateJWT($jwt) {                        //this function validates jwt token
    global $config;
    return JWT::decode($jwt, new Key($config['jwt_secret'], 'HS256'));
}

?>