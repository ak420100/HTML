<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo '<div class="alert">You must <a href="login.html">sign in</a> to create a habit.</div>';
    exit;
}

$theme = isset($_SESSION['theme']) ? $_SESSION['theme'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   
    <title>Create New Habit</title>
    <link rel="stylesheet" href="createhabstyle.css">
    <link rel="stylesheet" href="theme.css">

</head>
<body>



<div class="form-container">
    <h1>Create a New Habit</h1>
    <form id="habitForm" method="POST" action="createhab.php">
        <div class="form-group">
            <label for="habName">Habit Name:</label>
            <input type="text" id="habName" name="habName" placeholder="Enter habit name" required>
        </div>

        <div class="form-group">
            <label for="durationNumber">Duration:</label>
            <select id="durationNumber" name="durationNumber" required>
                <option value="" disabled selected>Select a number</option>
                <?php
                for ($i = 1; $i <= 31; $i++) {
                    echo "<option value=\"$i\">$i</option>";
                }
                ?>
            </select>

            <select id="durationUnit" name="durationUnit" required>
                <option value="" disabled selected>Select a unit</option>
                <option value="days">Days</option>
                <option value="months">Months</option>
                <option value="years">Years</option>
            </select>
        </div>

        <div class="form-group">
            <button type="submit">Create Habit</button>
        </div>
    </form>
</div>
<script src="loadSettings.js"></script>
<script src="loadTheme.js"></script>
</body>
</html>

<?php
// Backend Script for Creating a Habit (Assuming `createhab1.php`)

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'You must be logged in to create a habit.']);
    exit();
}

// Example: Insert habit into the database
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $habit_name = $_POST['habit_name'] ?? '';
    $duration = $_POST['duration'] ?? '';

    if (empty($habit_name) || empty($duration)) {
        echo json_encode(['error' => 'Habit name and duration are required.']);
        exit();
    }

    // Assuming a database connection is already established
    $user_id = $_SESSION['user_id'];
    $stmt = $db->prepare('INSERT INTO habits (user_id, habit_name, duration, created_at) VALUES (?, ?, ?, NOW())');
    $stmt->bind_param('iss', $user_id, $habit_name, $duration);

    if ($stmt->execute()) {
        echo json_encode(['success' => 'Habit created successfully.']);
    } else {
        echo json_encode(['error' => 'Failed to create habit.']);
    }
}
?>
