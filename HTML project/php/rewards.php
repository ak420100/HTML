<?php
include 'conn.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get current coin balance AND theme
    $stmt = $conn->prepare("SELECT coins, theme FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($coins, $theme);
    $stmt->fetch();
    $stmt->close();
    echo json_encode(['coins' => $coins, 'theme' => $theme]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $theme = $input['theme'] ?? '';
    $cost = $input['cost'] ?? 0;

    if (!$theme || $cost <= 0) {
        echo json_encode(['error' => 'Invalid request']);
        exit;
    }

    // Get user's coin balance
    $stmt = $conn->prepare("SELECT coins FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($coins);
    $stmt->fetch();
    $stmt->close();

    if ($coins < $cost) {
        echo json_encode(['error' => 'Not enough coins']);
        exit;
    }

    // Deduct cost and save theme
    $stmt = $conn->prepare("UPDATE users SET coins = coins - ?, theme = ? WHERE id = ?");
    $stmt->bind_param("isi", $cost, $theme, $user_id);

    if ($stmt->execute()) {
        echo json_encode([
            'success' => 'Theme applied!',
            'new_balance' => $coins - $cost,
            'theme' => $theme
        ]);
    } else {
        echo json_encode(['error' => 'Failed to apply theme']);
    }
    exit;
}
?>
