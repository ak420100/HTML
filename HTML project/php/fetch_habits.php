<?php
session_start();
header('Content-Type: application/json');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'You are not logged in']);
    exit();
}

// Database credentials
$servername = "mysql.hostinger.com";
$username = "u626296519_root1";
$password = "DatabasePassword2!";
$database = "u626296519_DB";

// Connect to the database
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

// Get the logged-in user's ID
$userId = $_SESSION['user_id'];

// Fetch habits and progress for the logged-in user
$sql = "SELECT name AS habit_name, duration, created_at, progress_count 
        FROM habits 
        WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();

$habits = [];
while ($row = $result->fetch_assoc()) {
    $habits[] = [
        'habit_name' => htmlspecialchars($row['habit_name']),
        'duration' => htmlspecialchars($row['duration']),
        'created_at' => htmlspecialchars($row['created_at']),
        'progress_status' => (int)$row['progress_count'] > 0 ? 'progress' : 'no-progress'
    ];
}

// Return the habits as JSON
echo json_encode($habits);

// Close the statement and connection
$stmt->close();
$conn->close();
?>