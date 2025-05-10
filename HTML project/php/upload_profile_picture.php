<?php
include 'conn.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "User not logged in."]);
    exit;
}

if (!isset($_FILES['profile_picture'])) {
    echo json_encode(["error" => "No file uploaded."]);
    exit;
}

$user_id = $_SESSION['user_id'];
$upload_dir = 'uploads/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$file_name = uniqid() . '_' . basename($_FILES['profile_picture']['name']);
$target_path = $upload_dir . $file_name;

if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_path)) {
    // Update user profile picture path in database
    $stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
    $stmt->bind_param("si", $target_path, $user_id);
    if ($stmt->execute()) {
        echo json_encode(["success" => "Profile picture updated.", "path" => $target_path]);
    } else {
        echo json_encode(["error" => "Failed to update database."]);
    }
} else {
    echo json_encode(["error" => "Failed to upload file."]);
}

?>
