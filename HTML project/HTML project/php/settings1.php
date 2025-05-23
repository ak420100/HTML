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
  <meta charset="UTF-8">
  <title>Settings</title>
  <link rel="stylesheet" href="settings.css">
</head>
<body class="<?php echo htmlspecialchars($theme . '-theme'); ?>">
  <div class="settings-container">
    <h1>User Settings</h1>
    <form id="settingsForm">
      <label class="toggle">
        <input type="checkbox" id="darkMode">
        <span class="slider"></span>
        <span class="label-text">Dark Mode</span>
      </label>

      <label class="toggle">
        <input type="checkbox" id="notifications">
        <span class="slider"></span>
        <span class="label-text">Enable Notifications</span>
      </label>

      <button type="submit">Save Changes</button>
      <p id="statusMessage"></p>
    </form>
  </div>

  <script>
    // Load settings for the logged-in user
    fetch('settings.php')
      .then(res => res.json())
      .then(data => {
        if (data.error) {
          alert(data.error);
          return;
        }
        const darkModeToggle = document.getElementById("darkMode");
        const notificationsToggle = document.getElementById("notifications");
  
        // Set toggle states
        darkModeToggle.checked = data.dark_mode == 1;
        notificationsToggle.checked = data.notification_enabled == 1;
  
        // Apply dark mode if enabled
        if (data.dark_mode == 1) {
          document.body.classList.add("dark-mode");
          document.querySelector(".settings-container").classList.add("dark-mode");
          document.querySelector("button").classList.add("dark-mode");
        }
  
        // Listen for dark mode toggle changes
        darkModeToggle.addEventListener("change", function () {
          if (darkModeToggle.checked) {
            document.body.classList.add("dark-mode");
            document.querySelector(".settings-container").classList.add("dark-mode");
            document.querySelector("button").classList.add("dark-mode");
          } else {
            document.body.classList.remove("dark-mode");
            document.querySelector(".settings-container").classList.remove("dark-mode");
            document.querySelector("button").classList.remove("dark-mode");
          }
        });
      });
  
    // Save settings for the logged-in user
    document.getElementById("settingsForm").addEventListener("submit", function (e) {
      e.preventDefault();
  
      const data = {
        dark_mode: document.getElementById("darkMode").checked ? 1 : 0,
        notification_enabled: document.getElementById("notifications").checked ? 1 : 0
      };
  
      fetch("settings.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data)
      })
        .then(res => res.json())
        .then(result => {
          const msg = document.getElementById("statusMessage");
          msg.textContent = result.success || result.error;
          msg.style.color = result.success ? "limegreen" : "red";
        });
    });
  </script>
  <script src="loadTheme.js"></script>
</body>
</html>
