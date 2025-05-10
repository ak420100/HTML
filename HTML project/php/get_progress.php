<?php
include 'conn.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT name, duration, progress_count FROM habits WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$progressData = [];

while ($row = $result->fetch_assoc()) {
    $percent = ($row['duration'] > 0) ? round(($row['progress_count'] / $row['duration']) * 100) : 0;
    $progressData[] = [
        'name' => $row['name'],
        'current' => $row['progress_count'],
        'total' => $row['duration'],
        'percent' => $percent
    ];
}

echo json_encode($progressData);
?>
