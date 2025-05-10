<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo '<div class="alert">You must <a href="login.html">sign in</a> to create a habit.</div>';
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <a href="index1.php" id="logo">Trabit</a>
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

</body>
</html>
