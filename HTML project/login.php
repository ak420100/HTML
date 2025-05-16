<?php
session_start();
include 'conn.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["error" => "Method Not Allowed"]);
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    echo json_encode(["error" => "Email and password are required."]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["error" => "Invalid email format."]);
    exit;
}

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

$stmt->bind_result($userId, $hashedPassword);
$stmt->fetch();

if (!password_verify($password, $hashedPassword)) {
    echo json_encode(["error" => "Invalid email or password."]);
    $stmt->close();
    $conn->close();
    exit;
}

$_SESSION['user_id'] = $userId;
$_SESSION['username'] = $email;

echo json_encode(["success" => "Login successful!", "userId" => $userId]);

$stmt->close();
$conn->close();
?>
