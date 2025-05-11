<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit();
}
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
            display: none; /* Hide by default */
        }
    </style>
</head>
<body>
    <div class="basic-notification" id="notification">
    </div>
    <script>
        // Fetch notifications and habits from the PHP script
        window.onload = () => {
            fetch('rewards.php')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    const notification = document.getElementById('notification');
                    if (data.notifications && Array.isArray(data.notifications) && data.notifications.length > 0) {
                        // Display notifications about habit progress
                        notification.innerHTML = data.notifications.join('<br>');
                        notification.style.display = 'block';
                        setTimeout(() => {
                            notification.style.display = 'none';
                        }, 10000);
                    }

                    // Display habits
                    const habitBlocks = document.getElementById('habit-blocks');
                    if (data.error) {
                        habitBlocks.innerHTML = `<p>${data.error}. <a href="login.html">Log in here</a>.</p>`;
                        return;
                    }

                    if (data.habits && Array.isArray(data.habits) && data.habits.length === 0) {
                        habitBlocks.innerHTML = '<p>No habits found. <a href="createhab.html">Start creating some!</a></p>';
                    } else if (data.habits && Array.isArray(data.habits)) {
                        data.habits.forEach(habit => {
                            const habitBlock = document.createElement('div');
                            habitBlock.className = 'habit-block';
                            habitBlock.innerHTML = `
                                <h2>${habit.habit_name}</h2>
                                <p>Duration: ${habit.duration}</p>
                                <p>Created at: ${habit.created_at}</p>
                            `;
                            habitBlock.addEventListener('click', () => {
                                window.location.href = `progress.html?habit_id=${habit.id}`;
                            });
                            habitBlocks.appendChild(habitBlock);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                    const notification = document.getElementById('notification');
                    notification.innerHTML = 'Error loading data. Please try again later.';
                    notification.style.display = 'block';
                    setTimeout(() => {
                        notification.style.display = 'none';
                    }, 10000);
                });
        };

        function logout() {
            fetch('logout.php', { method: 'POST' })
                .then(() => {
                    window.location.href = 'login.html';
                });
        }
    </script>
    <header>
        <div class="banner">
            <h1>Welcome to Trabit</h1>
        </div>
        <nav>
            <button onclick="goToPage('login.html')">Log in</button>
            <button onclick="goToPage('createhab1.php')">Habits</button>
            <button onclick="goToPage('account.html')">Account</button>
            <button onclick="goToPage('friendlist.html')">Friends List</button>
            <button onclick="goToPage('settings.html')">Settings</button>
            <button onclick="goToPage('rewards1.php')">Rewards</button>
            <button onclick="goToPage('progress.html')">Progress</button>
            
            <button onclick="logout()">Log out</button>
        </nav>
    </header>
    <section id="habit-blocks">
        <!-- Habit blocks will be inserted here by JavaScript -->
    </section>
    <script src="loadSettings.js"></script>

</body>
</html>
