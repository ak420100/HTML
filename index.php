<?php
session_start();
include 'conn.php'; // Include the database connection

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

// Retrieve the username from the session
$username = $_SESSION['username'];

// Create a connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Uncomment the line below for debugging
// echo "Connected successfully";

// Prepare the SQL query to fetch habits for the logged-in user
$sql = "SELECT 
    habits.name AS habit_name,
    habits.duration,
    habits.created_at
FROM
    users
JOIN
    habits ON users.id = habits.user_id
WHERE
    users.username = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

$habits = [];
while ($row = $result->fetch_assoc()) {
    $habits[] = $row;
}

echo json_encode($habits); // Return the data as JSON
$stmt->close();
$conn->close();
?>