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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Friend's Habit Progress</title>
  <link rel="stylesheet" href="theme.css" />
  <link rel="stylesheet" href="progress.css" />
</head>
<body>
  <header class="progress-header">
    <h1>Friend's Habit Progress</h1>
    <nav><a href="friendlist1.php">Back to Friends</a></nav>
  </header>

  <main class="progress-main">
    <section class="progress-visual-section">
      <h2>Their Habits</h2>
      <div id="progressContainer">Loading...</div>
    </section>
  </main>

  <footer class="progress-footer">
    <p>&copy; 2025 Habit Tracker</p>
  </footer>

  <script>
    const urlParams = new URLSearchParams(window.location.search);
    const friendId = urlParams.get('friend_id');

    if (!friendId) {
      document.getElementById('progressContainer').textContent = "Friend ID missing.";
    } else {
      fetch(`view_friend_habits.php?friend_id=${friendId}`)
        .then(res => res.json())
        .then(data => {
          const container = document.getElementById('progressContainer');
          container.innerHTML = '';

          if (data.error) {
            container.innerHTML = `<p>${data.error}</p>`;
            return;
          }

          data.forEach(habit => {
            const wrapper = document.createElement('div');
            wrapper.className = 'progress-wrapper';

            const title = document.createElement('p');
            const emoji = habit.percent === 100 ? '🎉' : habit.percent >= 50 ? '🔥' : '🌱';
            title.textContent = `${emoji} ${habit.name}: ${habit.progress} / ${habit.duration} (${habit.percent}%)`;

            const barOuter = document.createElement('div');
            barOuter.className = 'progress-bar-outer';

            const barInner = document.createElement('div');
            barInner.className = 'progress-bar-inner';
            setTimeout(() => {
                barInner.style.width = habit.percent + '%';
            }, 100);

            barOuter.appendChild(barInner);

            const challengeBtn = document.createElement('button');
            challengeBtn.textContent = 'Challenge';
            challengeBtn.className = 'challenge-button';
            challengeBtn.addEventListener('click', () => {
                const urlParams = new URLSearchParams(window.location.search);
                const friendId = urlParams.get('friend_id');
                window.location.href = `challenge1.php?friend_id=${friendId}&habit=${encodeURIComponent(habit.name)}`;
            });

            wrapper.appendChild(title);
            wrapper.appendChild(barOuter);
            wrapper.appendChild(challengeBtn);
            container.appendChild(wrapper);
            });

        });
    }
  </script>
</body>
</html>
