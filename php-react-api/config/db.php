<?php
// Local
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'new_ecom');

// Remote
// define('DB_HOST', 'host_name');
// define('DB_USER', 'asia');
// define('DB_PASS', '123');
// define('DB_NAME', 'mydb');

$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// $db->close();
?>