<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit();
}

$user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main Page</title>
    <link rel="stylesheet" href="indexstyle.css"> <!-- Link to CSS -->
    <script src="script.js"></script> <!-- Link to JavaScript -->
    <link rel="stylesheet" href="theme.css">
    <script>
    // Show the notification for a few seconds and then hide it
    document.addEventListener('DOMContentLoaded', () => {
        const notification = document.getElementById('notification');
        if (notification) {
            setTimeout(() => {
                notification.style.display = 'none';
            }, 5000); // Hide after 5 seconds
        }
    });

    function goToPage(page) {
        window.location.href = page;
    }
    </script>
    <style>
        .basic-notification {
            background-color: #f0f0f0;
            border: 1px solid #ccc;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            text-align: center;
            position: fixed;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            width: 90%;
            max-width: 400px;
        }
    </style>
    <script src="loadTheme.js" defer></script>

</head>

<body class="<?php echo htmlspecialchars($theme . '-theme'); ?>">
    <div class="basic-notification" id="notification">
        Hi, welcome to Trabit
    </div>
    <header>
        <div class="banner">
            <h1>Welcome to Trabit</h1>
        </div>
        <nav>
            <button onclick="goToPage('login.html')">Log in</button>
            <button onclick="goToPage('createhab1.php')">Habits</button>
            <button onclick="goToPage('account1.php')">Account</button>
            <button onclick="goToPage('friendlist1.php')">Friends List</button>
            <button onclick="goToPage('settings1.php')">Settings</button>
            <button onclick="goToPage('rewards1.php')">Rewards</button>
            <button onclick="goToPage('progress1.php')">Progress</button>
            <button onclick="logout()">Log out</button>
        </nav>
    </header>
    <section id="habit-blocks">
        <!-- Habit blocks will be inserted here by JavaScript -->
    </section>
    <script>
        // Fetch habits from the PHP script and display them
        fetch('fetch_habits.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                const habitBlocks = document.getElementById('habit-blocks');

                if (data.error) {
                    // Handle the case where the user is not logged in
                    habitBlocks.innerHTML = `<p>${data.error}. <a href="login.html">Log in here</a>.</p>`;
                    return;
                }

                if (data.length === 0) {
                    // Display a message if no habits are found
                    habitBlocks.innerHTML = '<p>No habits found. <a href="createhab1.php">Start creating some!</a></p>';
                } else {
                    data.forEach(habit => {
                        const habitBlock = document.createElement('div');
                        habitBlock.className = 'habit-block';
                        habitBlock.innerHTML = `
                            <h2>${habit.habit_name}</h2>
                            <p>Duration: ${habit.duration}</p>
                            <p>Created at: ${habit.created_at}</p>
                            <p>Status: ${habit.progress_status === 'progress' ? 'In Progress' : 'No Progress'}</p>
                        `;
                        // Add click event listener to redirect to progress.html
                        habitBlock.addEventListener('click', () => {
                            window.location.href = 'progress1.php';
                        });
                        habitBlocks.appendChild(habitBlock);
                    });
                }
            })
            .catch(error => {
                console.error('Error fetching habits:', error);
                document.getElementById('habit-blocks').innerHTML = '<p>Error loading habits. Please try again later.</p>';
            });

        function logout() {
            fetch('logout.php', { method: 'POST' })
                .then(() => {
                    window.location.href = 'login.html';
                });
        }
    </script>
    <script src="loadSettings.js"></script>
    <script src="loadTheme.js"></script>

</body>
</html>
