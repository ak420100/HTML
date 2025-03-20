<?php
include 'conn.php'; // Database connection

header('Content-Type: application/json');

// Allow only POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["error" => "Method Not Allowed"]);
    exit;
}

// Read and decode JSON input
$json = file_get_contents("php://input");
$data = json_decode($json, true);

// Debug: Log received JSON data
file_put_contents("debug_log.txt", json_encode($data, JSON_PRETTY_PRINT));

// Check if JSON decoding failed
if (!$data) {
    echo json_encode(["error" => "Invalid JSON received."]);
    exit;
}

// Extract and validate form data
$username = isset($data['username']) ? trim($data['username']) : '';
$email = isset($data['email']) ? trim($data['email']) : '';
$password = isset($data['password']) ? $data['password'] : '';

// Check for empty fields
if (empty($username) || empty($email) || empty($password)) {
    echo json_encode(["error" => "All fields are required."]);
    exit;
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["error" => "Invalid email format."]);
    exit;
}

// Check if email already exists
$checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$checkStmt->bind_param("s", $email);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows > 0) {
    echo json_encode(["error" => "Email is already registered."]);
    exit;
}
$checkStmt->close();

// Hash password
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

// Insert user data
$stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $username, $email, $hashedPassword);

if ($stmt->execute()) {
    echo json_encode(["success" => "Signup successful! Redirecting..."]);
} else {
    echo json_encode(["error" => "Database error: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
