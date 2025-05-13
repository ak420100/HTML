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
  <title>Habit Challenge</title>
  <link rel="stylesheet" href="theme.css" />
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(to right, #0f2027, #203a43, #2c5364);
      color: #fff;
      margin: 0;
      padding: 0;
    }

    .challenge-header {
      text-align: center;
      padding: 30px;
      background: rgba(0, 0, 0, 0.6);
    }

    .challenge-section {
      max-width: 800px;
      margin: 40px auto;
      padding: 30px;
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(8px);
      border-radius: 16px;
      box-shadow: 0 0 30px rgba(0, 0, 0, 0.3);
    }

    .progress-box {
      margin-bottom: 40px;
    }

    .progress-box h3 {
      margin-bottom: 10px;
      font-size: 20px;
    }

    .progress-bar-outer {
      height: 24px;
      background: #333;
      border-radius: 12px;
      overflow: hidden;
    }

    .progress-bar-inner {
      height: 100%;
      background: linear-gradient(to right, #00e676, #00c853);
      width: 0%;
      border-radius: 12px 0 0 12px;
      transition: width 0.5s ease;
    }

    .result-text {
      text-align: center;
      font-size: 18px;
      margin-top: 20px;
      font-weight: bold;
    }
  </style>
</head>
<body class="<?php echo htmlspecialchars($theme . '-theme'); ?>">
  <header class="challenge-header">
    <h1>Habit Challenge</h1>
    <p>See who reaches the goal first 💪</p>
  </header>

  <main class="challenge-section">
    <div id="habitName" style="text-align:center; font-size: 24px; margin-bottom: 30px;"></div>

    <div class="progress-box">
      <h3>Your Progress</h3>
      <div class="progress-bar-outer">
        <div class="progress-bar-inner" id="myProgress"></div>
      </div>
    </div>

    <div class="progress-box">
      <h3>Friend's Progress</h3>
      <div class="progress-bar-outer">
        <div class="progress-bar-inner" id="friendProgress"></div>
      </div>
    </div>

    <div class="result-text" id="resultText">Loading...</div>
  </main>

  <script>
    const params = new URLSearchParams(window.location.search);
    const friendId = params.get('friend_id');
    const habitName = decodeURIComponent(params.get('habit'));

    document.getElementById('habitName').textContent = `🏁 ${habitName.toUpperCase()} Challenge`;

    // Fetch both user and friend progress
    fetch(`get_challenge_data.php?friend_id=${friendId}&habit=${encodeURIComponent(habitName)}`)
      .then(res => res.json())
      .then(data => {
        if (data.error) {
          document.getElementById('resultText').textContent = data.error;
          return;
        }

        const myPercent = data.me.percent;
        const friendPercent = data.friend.percent;

        document.getElementById('myProgress').style.width = myPercent + '%';
        document.getElementById('friendProgress').style.width = friendPercent + '%';

        const result = document.getElementById('resultText');
        if (myPercent > friendPercent) {
          result.textContent = "🔥 You're leading!";
        } else if (friendPercent > myPercent) {
          result.textContent = "⚡ Your friend is ahead!";
        } else {
          result.textContent = "🤝 It's a tie!";
        }
      })
      .catch(err => {
        console.error(err);
        document.getElementById('resultText').textContent = "Failed to load challenge data.";
      });
  </script>
  <script src="loadTheme.js"></script>
</body>
</html>
