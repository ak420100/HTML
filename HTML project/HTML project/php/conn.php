<?php
$servername = "mysql.hostinger.com";
$username = "u626296519_root1";
$password = "DatabasePassword2!";
$database = "u626296519_DB";

// Create a connection
$conn = new mysqli($servername, $username, $password, $database);
$GLOBALS['conn'] = $conn;

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Uncomment the line below for debugging
// echo "Connected successfully";
?>
