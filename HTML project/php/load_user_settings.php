<?php
// filepath: vsls:/load_user_settings.php

session_start();
include 'conn.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "User not logged in."]);
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user settings
$stmt = $conn->prepare("SELECT dark_mode FROM settings WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($dark_mode);
$stmt->fetch();
$stmt->close();
$conn->close();

// Return dark mode preference
echo json_encode(["dark_mode" => $dark_mode]);
