<?php
session_start();
include 'conn.php';

if (!isset($_SESSION['user_id'])) {
    die("Error: User is not logged in.");
}

$userId = $_SESSION['user_id'];

// When form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $friendEmail = $_POST['friendEmail'] ?? '';
    $friendName = $_POST['friendName'] ?? '';
    $friendHabits = $_POST['friendHabits'] ?? '';

    if (empty($friendEmail) || empty($friendName)) {
        die("Error: Name and email are required.");
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
            header('Location: friendlist.html');
            exit;
        } else {
            die("Error inserting friend: " . $stmt->error);
        }

    } else {
        echo "Error: This email is not registered.";
    }
}
?>
