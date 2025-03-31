<?php
include 'conn.php';
session_start();

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Fetch the user's dark mode preference
    $stmt = $conn->prepare("SELECT dark_mode FROM settings WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($dark_mode);
    $stmt->fetch();
    $stmt->close();

    // Set the dark mode class if enabled
    $darkModeClass = $dark_mode == 1 ? 'dark-mode' : '';
} else {
    $darkModeClass = ''; // Default to light mode if not logged in
}
?>