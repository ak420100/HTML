<?php
include 'conn.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$input = json_decode(file_get_contents('php://input'), true);
$habit_name = $input['habit'] ?? '';

if (!$habit_name) {
    echo json_encode(["error" => "Missing habit name"]);
    exit;
}

$stmt = $conn->prepare("SELECT id, progress_count, duration, last_updated FROM habits WHERE user_id = ? AND name = ?");
$stmt->bind_param("is", $user_id, $habit_name);
$stmt->execute();
$result = $stmt->get_result();
$habit = $result->fetch_assoc();

if (!$habit) {
    echo json_encode(["error" => "Habit not found"]);
    exit;
}

// Check 24 hours
$now = new DateTime();
$last = $habit['last_updated'] ? new DateTime($habit['last_updated']) : new DateTime('1970-01-01');
$interval = $last->diff($now);

if ($interval->days == 0 && $interval->h < 24) {
    echo json_encode(["error" => "You can only mark progress once every 24 hours."]);
    exit;
}

// Update progress
$progress = $habit['progress_count'] + 1;
$update = $conn->prepare("UPDATE habits SET progress_count = ?, last_updated = NOW() WHERE id = ?");
$update->bind_param("ii", $progress, $habit['id']);
$success = $update->execute();

if ($success) {
    $percent = round(($progress / $habit['duration']) * 100);
    echo json_encode(["success" => "Progress updated", "progress" => $progress, "percent" => $percent]);
} else {
    echo json_encode(["error" => "Failed to update progress"]);
}
?>