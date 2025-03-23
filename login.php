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
$data = $_POST;


// Debug: Log received JSON data
file_put_contents("debug_log.txt", json_encode($data, JSON_PRETTY_PRINT));

// Check if JSON decoding failed
if (!$data) {
    echo json_encode(["error" => "Invalid JSON received."]);
    exit;
}

// Extract and validate form data
$email = isset($data['email']) ? trim($data['email']) : '';
$password = isset($data['password']) ? $data['password'] : '';

// Check for empty fields
if (empty($email) || empty($password)) {
    echo json_encode(["error" => "Email and password are required."]);
    exit;
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["error" => "Invalid email format."]);
    exit;
}

// Check if email exists in the database
$stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    echo json_encode(["error" => "Invalid email or password."]);
    $stmt->close();
    $conn->close();
    exit;
}

// Fetch user data
$stmt->bind_result($userId, $hashedPassword);
$stmt->fetch();

// Verify password
if (!password_verify($password, $hashedPassword)) {
    echo json_encode(["error" => "Invalid email or password."]);
    $stmt->close();
    $conn->close();
    exit;
}

// Successful login
echo json_encode(["success" => "Login successful!", "userId" => $userId]);

// If login is successful, store the username in the session
$_SESSION['username'] = $username;

// Redirect to index.html
header("Location: index.html");

$stmt->close();
$conn->close();
?>