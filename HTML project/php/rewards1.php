<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rewards</title>
    <link rel="stylesheet" href="rewardsstyle.css">
</head>
<body>
    <div class="container">
        <h1 class="friendsTitle">Rewards</h1>
        <div id="user-balance">Your Balance: <span id="coin-balance">0</span> Coins</div>
        <div id="claim-section">
            <button id="claim-coins-btn">Claim Daily Coins</button>
            <p id="claim-message"></p>
        </div>

        <h2>Select a Color Theme</h2>
        <div id="color-themes">
            <button class="theme-button" data-theme="pink" data-cost="100" style="background-color: pink;">Pink (100 Coins)</button>
            <button class="theme-button" data-theme="blue" data-cost="120" style="background-color: lightblue;">Blue (120 Coins)</button>
            <button class="theme-button" data-theme="green" data-cost="150" style="background-color: lightgreen;">Green (150 Coins)</button>
            <button class="theme-button" data-theme="purple" data-cost="130" style="background-color: violet;">Purple (130 Coins)</button>
            <button class="theme-button" data-theme="orange" data-cost="110" style="background-color: orange;">Orange (110 Coins)</button>
            <button class="theme-button" data-theme="gray" data-cost="90" style="background-color: lightgray;">Gray (90 Coins)</button>
        </div>
    </div>

    <script src="rewards.js"></script>
    <script src="loadTheme.js" defer></script>
</body>
</html>