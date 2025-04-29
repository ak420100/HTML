<?php
session_start();
include 'conn.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$userId = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT friend_id, email FROM friends WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$friends = [];
while ($row = $result->fetch_assoc()) {
    $friends[] = $row;
}

$stmt->close();

header('Content-Type: application/json');
echo json_encode($friends);
?>
