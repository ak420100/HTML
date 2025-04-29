<?php
$servername = "mysql.hostinger.com"; // Hostinger's MySQL server
$username = "u626296519_root1"; // Your database username
$password = "DatabasePassword2!"; // Your database password
$database = "u626296519_DB"; // Your database name

// Create a connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Uncomment the line below for debugging
// echo "Connected successfully";
?>
