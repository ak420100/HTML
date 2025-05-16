<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$theme = isset($_SESSION['theme']) ? $_SESSION['theme'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account - Habit Tracker</title>
    <link rel="stylesheet" href="account.css">
    <link rel="stylesheet" href="theme.css">
    <link rel="stylesheet" href="settings.css">
</head>
<body>
    <header class="account-header-section">
        <h1>Habit Tracker</h1>
        <nav class="nav-section">
            <ul class="account-links">
                <li class="account-item"><a href="index1.php">Home</a></li>
                <li class="account-item"><a href="account1.php">Account</a></li>
                <li class="account-item"><a href="logout.html">Logout</a></li>
            </ul>
        </nav>
    </header>
    
    <main class="account-main-section">
        
        
        
    </main>
    
    <footer class="account-footer-section">
        <p>&copy; 2025 Habit Tracker. All rights reserved.</p>
    </footer>
    <script src="loadSettings.js"></script>

</body>
</html>