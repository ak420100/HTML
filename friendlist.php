<?php
include 'conn.php'; // Make sure this file connects to your database

// TODO: Replace this with actual logged-in user ID (e.g., from session)
$currentUserId = 1;

// Add friend if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['friendName'], $_GET['friendEmail'])) {
    $friendName = trim($_GET['friendName']);
    $friendEmail = trim($_GET['friendEmail']);
    $friendHabits = trim($_GET['friendHabits']);

    // 1. Find the friend's user ID by email
    $friendId = null;
    $findStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $findStmt->bind_param("s", $friendEmail);
    $findStmt->execute();
    $findStmt->bind_result($friendId);
    $findStmt->fetch();
    $findStmt->close();

    if ($friendId) {
        // 2. Insert the friendship
        $stmt = $conn->prepare("INSERT INTO friends (user_id, friend_id, status, created_at, email) VALUES (?, ?, 'pending', NOW(), ?)");
        $stmt->bind_param("iis", $currentUserId, $friendId, $friendEmail);

        if ($stmt->execute()) {
            echo "<p style='color:green;'>Friend added successfully!</p>";
        } else {
            echo "<p style='color:red;'>Error adding friend: " . $stmt->error . "</p>";
        }

        $stmt->close();
    } else {
        echo "<p style='color:red;'>No user found with the email '$friendEmail'.</p>";
    }
}

// 3. Get all friends for this user
$sql = "SELECT 
            f.email, 
            u.username AS friend_name, 
            GROUP_CONCAT(h.name SEPARATOR ', ') AS habits
        FROM friends f
        LEFT JOIN users u ON f.friend_id = u.id
        LEFT JOIN habits h ON h.user_id = f.friend_id
        WHERE f.user_id = ?
        GROUP BY f.friend_id, f.email";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $currentUserId);
$stmt->execute();
$result = $stmt->get_result();
?>

<!-- Start of HTML Table -->
<table class="friendsTable">
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>All Habits</th>
        </tr>
    </thead>
    <tbody id="friendsList">
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['friend_name'] ?? 'Unknown') ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['habits'] ?? 'No habits') ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php
$stmt->close();
$conn->close();
?>
