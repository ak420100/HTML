<?php
include 'conn.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$reward_amount = 50;

// Get last claimed time
$stmt = $conn->prepare("SELECT coins, last_claimed FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($coins, $last_claimed);
$stmt->fetch();
$stmt->close();

$now = new DateTime();
$lastClaim = $last_claimed ? new DateTime($last_claimed) : null;

if ($lastClaim && $now->getTimestamp() - $lastClaim->getTimestamp() < 86400) {
    $nextClaim = $lastClaim->modify('+24 hours')->format('Y-m-d H:i:s');
    echo json_encode(['error' => 'Already claimed today', 'next_claim' => $nextClaim]);
    exit;
}

// Add coins and update last_claimed
$stmt = $conn->prepare("UPDATE users SET coins = coins + ?, last_claimed = NOW() WHERE id = ?");
$stmt->bind_param("ii", $reward_amount, $user_id);
if ($stmt->execute()) {
    echo json_encode(['success' => "Claimed $reward_amount coins!", 'new_balance' => $coins + $reward_amount]);
} else {
    echo json_encode(['error' => 'Claim failed']);
}
?>
