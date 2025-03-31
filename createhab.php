<?php
include 'conn.php';
session_start();

$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    die("Error: User is not logged in.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $habName = $_POST["habName"] ?? '';
    $habDuration = $_POST["durationNumber"] ?? '';
    $durationUnit = $_POST["durationUnit"] ?? '';

    if (empty($habName) || empty($habDuration) || empty($durationUnit)) {
        die("Error: All fields are required!");
    }

    $stmt = $conn->prepare("INSERT INTO habits (user_id, name, duration, duration_unit) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("isss", $userId, $habName, $habDuration, $durationUnit);

    if ($stmt->execute()) {
        echo "Habit created successfully!";
        header("Location: index.html");
    } else {
        die("Error executing statement: " . $stmt->error);
    }

    $stmt->close();
}

$conn->close();
?>
