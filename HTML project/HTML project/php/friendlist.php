<?php
session_start();
include 'conn.php';

if (!isset($_SESSION['user_id'])) {
    die(json_encode(['error' => 'User is not logged in.']));
}

$userId = $_SESSION['user_id'];

// When form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $friendEmail = $_POST['friendEmail'] ?? '';
    $friendName = $_POST['friendName'] ?? '';
    $friendHabits = $_POST['friendHabits'] ?? '';

    if (empty($friendEmail) || empty($friendName)) {
        echo json_encode(['error' => 'Name and email are required.']);
        exit;
    }

    // Try to find the friend's user ID based on their email
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $friendEmail);
    $stmt->execute();
    $result = $stmt->get_result();
    $friend = $result->fetch_assoc();
    $stmt->close();

    if ($friend) {
        $friendId = $friend['id'];

        // Insert into friends table
        $stmt = $conn->prepare("INSERT INTO friends (user_id, friend_id, status, email) VALUES (?, ?, 'pending', ?)");
        $stmt->bind_param("iis", $userId, $friendId, $friendEmail);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['error' => 'Error inserting friend: ' . $stmt->error]);
        }
    } else {
        echo json_encode(['error' => 'This email is not registered.', 'field' => 'friendEmail']);
    }
    exit;
}
?>
