<?php
include 'conn.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$friendId = $_GET['friend_id'] ?? 0;
$friendId = intval($friendId);

if (!$friendId) {
    echo json_encode(['error' => 'Friend ID missing.']);
    exit;
}

// Verify this friend is actually connected to you
$stmt = $conn->prepare("SELECT * FROM friends WHERE user_id = ? AND friend_id = ?");
$stmt->bind_param("ii", $_SESSION['user_id'], $friendId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['error' => 'You are not friends with this user.']);
    exit;
}

// Get friend's habits
$stmt = $conn->prepare("SELECT name, duration, progress_count FROM habits WHERE user_id = ?");
$stmt->bind_param("i", $friendId);
$stmt->execute();
$res = $stmt->get_result();

$habits = [];
while ($row = $res->fetch_assoc()) {
    $percent = ($row['duration'] > 0) ? round(($row['progress_count'] / $row['duration']) * 100) : 0;
    $habits[] = [
        'name' => $row['name'],
        'progress' => $row['progress_count'],
        'duration' => $row['duration'],
        'percent' => $percent
    ];
}

echo json_encode($habits);
?>
