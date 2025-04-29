<?php
include 'conn.php';
session_start();

header('Content-Type: application/json');

$dark = false;

if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT dark_mode FROM settings WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $stmt->bind_result($dark_mode);
    $stmt->fetch();
    $stmt->close();

    $dark = ($dark_mode == 1);
}

echo json_encode(["dark" => $dark]);
