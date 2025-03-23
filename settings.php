<?php
include 'conn.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$user_id = 1; // Simulate logged-in user

$dark_mode = $data['dark_mode'] ?? 0;
$notification_enabled = $data['notification_enabled'] ?? 0;

// Update settings
$stmt = $conn->prepare("UPDATE settings SET dark_mode = ?, notification_enabled = ? WHERE user_id = ?");
$stmt->bind_param("iii", $dark_mode, $notification_enabled, $user_id);
if ($stmt->execute()) {
    echo json_encode(["success" => "Settings updated."]);
} else {
    echo json_encode(["error" => "Failed to update settings."]);
}
?>
