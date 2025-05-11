<?php
include 'conn.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'You must be logged in.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$friend_id = isset($_GET['friend_id']) ? intval($_GET['friend_id']) : 0;
$habit_name = $_GET['habit'] ?? '';

if (!$friend_id || !$habit_name) {
    echo json_encode(['error' => 'Missing friend ID or habit name.']);
    exit;
}

// Get friend's habit
$stmt2 = $conn->prepare("SELECT name, duration, duration_unit, progress_count FROM habits WHERE user_id = ? AND name = ?");
$stmt2->bind_param("is", $friend_id, $habit_name);
$stmt2->execute();
$res2 = $stmt2->get_result();
$friend = $res2->fetch_assoc();

// If friend doesn't have the habit
if (!$friend) {
    echo json_encode(['error' => 'Your friend does not have this habit.']);
    exit;
}

// Check if YOU have the habit
$stmt1 = $conn->prepare("SELECT progress_count, duration FROM habits WHERE user_id = ? AND name = ?");
$stmt1->bind_param("is", $user_id, $habit_name);
$stmt1->execute();
$res1 = $stmt1->get_result();
$me = $res1->fetch_assoc();

// If YOU don't have the habit, copy it from your friend
if (!$me) {
    $duration = $friend['duration'];
    $unit = $friend['duration_unit'];

    $insert = $conn->prepare("INSERT INTO habits (user_id, name, duration, duration_unit, progress_count) VALUES (?, ?, ?, ?, 0)");
    $insert->bind_param("isis", $user_id, $habit_name, $duration, $unit);
    $insert->execute();

    // Re-fetch your habit
    $stmt1 = $conn->prepare("SELECT progress_count, duration FROM habits WHERE user_id = ? AND name = ?");
    $stmt1->bind_param("is", $user_id, $habit_name);
    $stmt1->execute();
    $res1 = $stmt1->get_result();
    $me = $res1->fetch_assoc();
}

// Calculate progress %
function calculate_percent($progress, $total) {
    return $total > 0 ? round(($progress / $total) * 100) : 0;
}

$response = [
    'me' => [
        'progress' => $me['progress_count'],
        'duration' => $me['duration'],
        'percent' => calculate_percent($me['progress_count'], $me['duration'])
    ],
    'friend' => [
        'progress' => $friend['progress_count'],
        'duration' => $friend['duration'],
        'percent' => calculate_percent($friend['progress_count'], $friend['duration'])
    ]
];

echo json_encode($response);
?>
