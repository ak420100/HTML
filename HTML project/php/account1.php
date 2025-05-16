<?php
ob_start();
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
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
    <link rel="stylesheet" href="settings.css"> <!-- or another page-specific CSS -->

</head>
<body class="<?php echo htmlspecialchars($theme . '-theme'); ?>">
    <header class="account-header-section">
        <h1>Habit Tracker</h1>
        <nav class="nav-section">
            <ul class="account-links">
                <li class="account-item"><a href="index1.php">Home</a></li>
                <li class="account-item"><a href="logout.html">Logout</a></li>
            </ul>
        </nav>
    </header>
    
    <main class="account-main-section">

        <section id="account-info" class="account-info-section">
            <h2>Account Information</h2>
            <div class="profile-picture-section">
                <img src="profile-placeholder.png" alt="Profile Picture" class="profile-picture">
            </div>
            <div class="account-details">
                <p><strong>Username:</strong> JohnDoe</p>
                <p><strong>Email:</strong> john.doe@example.com</p>
                <p><strong>Phone Number:</strong> +123456789</p>
            </div>
        </section>

        <section id="account-info-update" class="account-info-update-section">
            <h3>Update Your Information</h3>
            <div class="profile-picture-section">
        <img src="profile-placeholder.png" alt="Profile Picture" class="profile-picture">
        <button type="button" class="upload-button">Upload New Picture</button>
        <input type="file" id="profilePicInput" style="display:none;" accept="image/*">

        </div>
            <form id="account-form">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
                
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>

                
                <label for="new-password">New Password:</label>
                <input type="password" id="new-password" name="new-password" required>

                <label for="cofirm-password">Confirm Password:</label>
                <input type="password" id="confirm-password" name="confirm-password" required>

                <button type="submit">Update</button>
            </form>
        </section>


        <section id="account-habit-update" class="habit-update-section">
    <h2>Update Your Habits</h2>
    <form id="habit-form">
        <label for="habit1">Habit 1:</label>
        <input type="text" id="habit1" name="habit1" value="Exercise" required>

        <label for="habit2">Habit 2:</label>
        <input type="text" id="habit2" name="habit2" value="Read" required>

        <label for="habit3">Habit 3:</label>
        <input type="text" id="habit3" name="habit3" value="Meditate" required>

        <label for="habit4">Habit 4:</label>
        <input type="text" id="habit4" name="habit4" value="Drink Water" required>

        <label for="habit5">Habit 5:</label>
        <input type="text" id="habit5" name="habit5" value="Wake Up Early" required>

        <button type="submit">Save Habits</button>
    </form>
</section>
    </main>
    
    <footer class="account-footer-section">
        <p>&copy; 2025 Habit Tracker. All rights reserved.</p>
    </footer>
    <script>
        document.getElementById('account-form').addEventListener('submit', function(e) {
            e.preventDefault();
        
            const accountData = {
                username: document.getElementById('username').value,
                email: document.getElementById('email').value,
                phonenumber: document.getElementById('phonenumber').value,
                new_password: document.getElementById('new-password').value,
                confirm_password: document.getElementById('confirm-password').value
            };
        
            fetch('account.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(accountData)
            })
            .then(res => res.json())
            .then(result => {
                if (result.success) {
                    alert(result.success);
                } else {
                    alert(result.error);
                }
            });
        });
        
        // Habit form
        document.getElementById('habit-form').addEventListener('submit', function(e) {
            e.preventDefault();
        
            const habits = [
                document.getElementById('habit1').value,
                document.getElementById('habit2').value,
                document.getElementById('habit3').value,
                document.getElementById('habit4').value,
                document.getElementById('habit5').value
            ];
        
            fetch('account.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ habits })
            })
            .then(res => res.json())
            .then(result => {
                if (result.success) {
                    alert(result.success);
                } else {
                    alert(result.error);
                }
            });
        });
    </script>
    <script>
        document.querySelector('.upload-button').addEventListener('click', function() {
            document.getElementById('profilePicInput').click();
        });
    </script>
    <script>
        document.querySelector('.upload-button').addEventListener('click', function() {
            document.getElementById('profilePicInput').click();
        });
        
        document.getElementById('profilePicInput').addEventListener('change', function() {
            const file = this.files[0];
            if (!file) return;
        
            const formData = new FormData();
            formData.append('profile_picture', file);
        
            fetch('upload_profile_picture.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(result => {
                if (result.success) {
                    alert('Profile picture updated!');
                    location.reload(); // reload page to show new picture
                } else {
                    alert(result.error);
                }
            });
        });
    </script>
    <script src="loadTheme.js"></script>
</body>
</html>