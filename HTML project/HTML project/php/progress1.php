<?php
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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Your Habit Progress</title>
  <link rel="stylesheet" href="theme.css" />
  <link rel="stylesheet" href="progress.css" />
</head>
<body class="<?php echo htmlspecialchars($theme . '-theme'); ?>">
  <header class="progress-header">
    <h1>Habit Progress</h1>
    <nav>
      <a href="index1.php">Back to Main Page</a>
    </nav>
  </header>

  <!-- Added habit notifications container -->
  <div id="habit-notifications" style="position: fixed; top: 20px; left: 50%; transform: translateX(-50%);
  display: flex; flex-direction: column; gap: 10px; z-index: 9999;"></div>

  <main class="progress-main">
    <div id="messageBar" style="display:none; text-align:center; padding:10px; font-weight:bold;"></div>

    <section class="progress-visual-section">
      <h2>Your Habits</h2>
      <div id="progressContainer" class="progress-cards"></div>
    </section>
  </main>
  
  <footer class="progress-footer">
    <p>&copy; 2025 Habit Tracker</p>
  </footer>

  <script>
    fetch('get_progress.php')
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
          wrapper.style.cursor = 'pointer';
  
          const title = document.createElement('p');
          const emoji = habit.percent === 100 ? '🎉' : habit.percent >= 50 ? '🔥' : '🌱';
          title.textContent = `${emoji} ${habit.name}: ${habit.current} / ${habit.total} (${habit.percent}%)`;
  
          const barOuter = document.createElement('div');
          barOuter.className = 'progress-bar-outer';
  
          const barInner = document.createElement('div');
          barInner.className = 'progress-bar-inner';
          setTimeout(() => {
            barInner.style.width = habit.percent + '%';
          }, 100);
  
          barOuter.appendChild(barInner);
          wrapper.appendChild(title);
          wrapper.appendChild(barOuter);
          container.appendChild(wrapper);
  
          // ✅ Handle click to increment progress
          wrapper.addEventListener('click', () => {
            fetch('increment_progress.php', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ habit: habit.name })
            })
              .then(res => res.json())
              .then(response => {
                if (response.success) {
                  const messageBar = document.getElementById('messageBar');
                  const notifications = document.getElementById('habit-notifications');

                  // ✅ Update the progress immediately
                  const newPercent = response.percent;
                  const newCurrent = response.progress;
                  title.textContent = `${emoji} ${habit.name}: ${newCurrent} / ${habit.total} (${newPercent}%)`;
                  barInner.style.width = newPercent + '%';

                  // ✅ Display a notification for the last updated habit
                  const notification = document.createElement('div');
                  notification.textContent = `✅ Last updated: ${habit.name} (${newPercent}%)`;
                  notification.style.background = '#4caf50';
                  notification.style.color = '#fff';
                  notification.style.padding = '10px';
                  notification.style.borderRadius = '5px';
                  notifications.innerHTML = ''; // Clear previous notifications
                  notifications.appendChild(notification);

                  // Remove the notification after 3 seconds
                  setTimeout(() => {
                    notifications.removeChild(notification);
                  }, 3000);

                  // ✅ Save the last updated habit to localStorage
                  localStorage.setItem('lastUpdatedHabit', JSON.stringify({
                    name: habit.name,
                    percent: newPercent,
                    current: newCurrent,
                    total: habit.total
                  }));
                } else {
                  alert(response.error);
                }
              })
              .catch(err => {
                alert('Something went wrong.');
                console.error(err);
              });
          });
        });
      });

    // New script for login progress notifications
    window.addEventListener("DOMContentLoaded", () => {
      fetch('get_login_progress.php')
        .then(res => res.json())
        .then(data => {
          if (!Array.isArray(data)) return;

          const container = document.getElementById('habit-notifications');

          data.forEach(item => {
            const messageBox = document.createElement('div');
            messageBox.style.padding = "10px 20px";
            messageBox.style.borderRadius = "8px";
            messageBox.style.fontSize = "15px";
            messageBox.style.color = "#fff";
            messageBox.style.background = item.status === 'progress' ? "#4caf50" : "#ff9800";
            messageBox.style.boxShadow = "0 4px 10px rgba(0,0,0,0.2)";
            messageBox.style.opacity = "0";
            messageBox.style.transition = "opacity 0.4s ease";
            messageBox.textContent = item.status === 'progress'
              ? `✅ You've made progress on ${item.habit}!`
              : `⏳ No progress yet on ${item.habit}. Try updating today!`;

            container.appendChild(messageBox);
            setTimeout(() => messageBox.style.opacity = "1", 100); // fade in
            setTimeout(() => {
              messageBox.style.opacity = "0";
              setTimeout(() => messageBox.remove(), 1000); // fade out + remove
            }, 10000);
          });
        });
    });

    // ✅ Load the last updated habit on page load
    window.addEventListener('DOMContentLoaded', () => {
      const lastUpdatedHabit = JSON.parse(localStorage.getItem('lastUpdatedHabit'));
      if (lastUpdatedHabit) {
        const notifications = document.getElementById('habit-notifications');
        const notification = document.createElement('div');
        notification.textContent = `📌 Last updated: ${lastUpdatedHabit.name} (${lastUpdatedHabit.percent}%)`;
        notification.style.background = '#2196f3';
        notification.style.color = '#fff';
        notification.style.padding = '10px';
        notification.style.borderRadius = '5px';
        notifications.appendChild(notification);
      }
    });
  </script>
  <script src="loadTheme.js"></script>
</body>
</html>
