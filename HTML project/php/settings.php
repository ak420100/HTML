<?php
include 'conn.php';
header('Content-Type: application/json');
session_start(); 

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "User not logged in."]);
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Saving settings
    $data = json_decode(file_get_contents("php://input"), true);
    $dark_mode = $data['dark_mode'] ?? 0;
    $notification_enabled = $data['notification_enabled'] ?? 0;

    $stmt = $conn->prepare("SELECT id FROM settings WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt = $conn->prepare("UPDATE settings SET dark_mode = ?, notification_enabled = ? WHERE user_id = ?");
        $stmt->bind_param("iii", $dark_mode, $notification_enabled, $user_id);
    } else {
        $stmt = $conn->prepare("INSERT INTO settings (user_id, dark_mode, notification_enabled) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $user_id, $dark_mode, $notification_enabled);
    }

    if ($stmt->execute()) {
        echo json_encode(["success" => "Settings updated."]);
    } else {
        echo json_encode(["error" => "Failed to update settings."]);
    }
    $stmt->close();

} else {
    // Fetching settings
    $stmt = $conn->prepare("SELECT dark_mode, notification_enabled FROM settings WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $settings = $result->fetch_assoc();

    if ($settings) {
        echo json_encode($settings);
    } else {
        echo json_encode(["dark_mode" => 0, "notification_enabled" => 0]);
    }
    $stmt->close();
}

$conn->close();
?>
