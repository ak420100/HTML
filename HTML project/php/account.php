<?php
include 'conn.php';
session_start();

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "You must be logged in."]);
    exit;
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents("php://input"), true);

// Validate received data
$username = $data['username'] ?? '';
$email = $data['email'] ?? '';
$new_password = $data['new_password'] ?? '';
$confirm_password = $data['confirm_password'] ?? '';

if ($new_password !== $confirm_password) {
    echo json_encode(["error" => "Passwords do not match."]);
    exit;
}

// Hash the password
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

// Update user info
$stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, phonenumber = ?, password = ? WHERE id = ?");
$stmt->bind_param("ssssi", $username, $email, $phonenumber, $hashed_password, $user_id);

if (!$stmt->execute()) {
    echo json_encode(["error" => "Failed to update account info."]);
    exit;
}

// Optional: Update user's habits if habit data is sent
if (isset($data['habits']) && is_array($data['habits'])) {
    // Delete existing habits
    $conn->query("DELETE FROM habits WHERE user_id = $user_id");

    // Insert new habits
    $stmt = $conn->prepare("INSERT INTO habits (user_id, habit_name) VALUES (?, ?)");
    foreach ($data['habits'] as $habit) {
        if (!empty($habit)) {
            $stmt->bind_param("is", $user_id, $habit);
            if (!$stmt->execute()) {
                echo json_encode(["error" => "Failed to insert habit: $habit"]);
                $stmt->close();
                $conn->close();
                exit;
            } 
        }
    }
}

echo json_encode(["success" => "Account updated successfully."]);

$stmt->close();
$conn->close();
?>
