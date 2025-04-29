<?php
    include 'conn.php';
    header('Content-Type: application/json');
    session_start(); // Start session to get logged-in user

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(["error" => "User not logged in."]);
        exit;
    }

    $user_id = $_SESSION['user_id']; // Get logged-in user's ID
    $data = json_decode(file_get_contents("php://input"), true);

    $dark_mode = $data['dark_mode'] ?? 0;
    $notification_enabled = $data['notification_enabled'] ?? 0;

    // Check if settings already exist for the user
    $stmt = $conn->prepare("SELECT id FROM settings WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Update existing settings
        $stmt = $conn->prepare("UPDATE settings SET dark_mode = ?, notification_enabled = ? WHERE user_id = ?");
        $stmt->bind_param("iii", $dark_mode, $notification_enabled, $user_id);
    } else {
        // Insert new settings
        $stmt = $conn->prepare("INSERT INTO settings (user_id, dark_mode, notification_enabled) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $user_id, $dark_mode, $notification_enabled);
    }

    if ($stmt->execute()) {
        echo json_encode(["success" => "Settings updated."]);
    } else {
        echo json_encode(["error" => "Failed to update settings."]);
    }

    $stmt->close();
    $conn->close();
?>