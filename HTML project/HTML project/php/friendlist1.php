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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Friends</title>
    <link rel="stylesheet" href="friendliststyle.css">
    <link rel="stylesheet" href="theme.css">
    <link rel="stylesheet" href="settings.css">
</head>
<body class="<?php echo htmlspecialchars($theme . '-theme'); ?>">



<div class="container">
    <h1 class="friendsTitle">Your Friends</h1>
    <div class="friendContainer">
        <form id="friendForm" class="addFriendForm">

            <label for="friendName">Name:</label>
            <input type="text" id="friendName" name="friendName" required>

            <label for="friendEmail">Email:</label>
            <input type="email" id="friendEmail" name="friendEmail" required>

            <label for="friendHabits">Habits:</label>
            <input type="text" id="friendHabits" name="friendHabits">

            <button type="submit" id="addFriend">Add Friend</button>
        </form>

        <table class="friendsTable">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>All Habits</th>
                </tr>
            </thead>
            <tbody id="friendsList">
                <!-- Friends will be added here -->
            </tbody>
        </table>
    </div>
</div>


<script>
    document.addEventListener("DOMContentLoaded", function() {
      fetchFriends();
    });
  
    function fetchFriends() {
      fetch('loadfriends.php')
        .then(response => response.json())
        .then(friends => {
          const table = document.getElementById('friendsList');
          table.innerHTML = '';
  
          friends.forEach(friend => {
            const row = document.createElement('tr');
  
            row.innerHTML = `
              <td>${friend.friend_id}</td>
              <td>${friend.email}</td>
              <td><button class="view-habits-btn" data-id="${friend.friend_id}">View Habits</button></td>
            `;
  
            table.appendChild(row);
  
            // ✅ Redirect to friend's progress page
            row.querySelector('.view-habits-btn').addEventListener('click', function () {
              const friendId = this.dataset.id;
              window.location.href = `friendprogress1.php?friend_id=${friendId}`;
            });
          });
        })
        .catch(error => console.error('Error loading friends:', error));
    }
    document.getElementById('friendForm').addEventListener('submit', function(e) {
        e.preventDefault(); // ⛔ Stop the default form submission

        const formData = new FormData(this);

        fetch('friendlist.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(result => {
            if (result.success) {
                alert("✅ Friend added successfully!");
                fetchFriends(); // Refresh the table
                document.getElementById('friendForm').reset();
            } else {
                alert("❌ " + (result.error || "Something went wrong."));
            }
        })
        .catch(error => {
            console.error("Error adding friend:", error);
            alert("❌ Failed to add friend.");
        });
    });

  </script>
  
    
<script src="loadSettings.js"></script>
<script src="loadTheme.js"></script>

</body>
</html>
