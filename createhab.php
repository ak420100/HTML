<?php
include 'conn.php'; // Include the database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $habName = $_POST["habName"] ?? '';
    $habDuration = $_POST["habDuration"] ?? '';

    if (empty($habName) || empty($habDuration)) {
        die("Error: All fields are required!");
    }

    $stmt = $conn->prepare("INSERT INTO habits (name, duration) VALUES (?, ?)");
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("si", $habName, $habDuration);

    if ($stmt->execute()) {
        echo "Habit created successfully!";
    } else {
        die("Error executing statement: " . $stmt->error);
    }

    $stmt->close();
}

$conn->close();
?>
