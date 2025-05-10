<?php
session_start();
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

</head>
<body>
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
    <script>
        // Fetch habits from the PHP script and display them
        fetch('index.php')
            .then(response => {
                console.log('Raw response:', response);
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Parsed JSON:', data);
                const habitBlocks = document.getElementById('habit-blocks');

                if (data.error) {
                    // Handle the case where the user is not logged in
                    habitBlocks.innerHTML = `<p>${data.error}. <a href="login.html">Log in here</a>.</p>`;
                    return;
                }

                if (data.length === 0) {
                    // Display a message if no habits are found
                    habitBlocks.innerHTML = '<p>No habits found. <a href="createhab.html">Start creating some!</a></p>';
                } else {
                    data.forEach(habit => {
                        const habitBlock = document.createElement('div');
                        habitBlock.className = 'habit-block';
                        habitBlock.innerHTML = `
                            <h2>${habit.habit_name}</h2>
                            <p>Duration: ${habit.duration}</p>
                            <p>Created at: ${habit.created_at}</p>
                        `;
                        // Add click event listener to redirect to progress.html
                        habitBlock.addEventListener('click', () => {
                            window.location.href = 'progress.html';
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

</body>
</html>
