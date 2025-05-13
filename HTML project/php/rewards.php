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
    // Get current coin balance, theme, and purchased themes
    $stmt = $conn->prepare("SELECT coins, theme, purchased_themes FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($coins, $theme, $purchased_themes);
    $stmt->fetch();
    $stmt->close();



    echo json_encode([
        'coins' => $coins,
        'theme' => $theme,
        'purchased_themes' => $purchased_themes ? explode(',', $purchased_themes) : []
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $theme = $input['theme'] ?? '';
    $cost = isset($input['cost']) ? (int)$input['cost'] : -1;

    if (!$theme || $cost < 0) {
        echo json_encode(['error' => 'Invalid request']);
        exit;
    }

    // Get user's coins and purchased themes
    $stmt = $conn->prepare("SELECT coins, purchased_themes FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($coins, $purchased_themes);
    $stmt->fetch();
    $stmt->close();

    $purchased_themes_array = $purchased_themes ? explode(',', $purchased_themes) : [];

    // 🟢 If already purchased and cost is 0, just update theme
    if (in_array($theme, $purchased_themes_array) && $cost === 0) {
        $stmt = $conn->prepare("UPDATE users SET theme = ? WHERE id = ?");
        $stmt->bind_param("si", $theme, $user_id);
        if ($stmt->execute()) {
            echo json_encode(['success' => 'Theme applied!', 'new_balance' => $coins, 'theme' => $theme]);
        } else {
            echo json_encode(['error' => 'Failed to update theme']);
        }
        exit;
    }

    // 🔴 Not enough coins
    if ($coins < $cost) {
        echo json_encode(['error' => 'Not enough coins']);
        exit;
    }

    // 🟡 New purchase: Deduct coins, update theme and purchased list
    $purchased_themes_array[] = $theme;
    $updated_purchased_themes = implode(',', array_unique($purchased_themes_array));

    $stmt = $conn->prepare("UPDATE users SET coins = coins - ?, theme = ?, purchased_themes = ? WHERE id = ?");
    $stmt->bind_param("issi", $cost, $theme, $updated_purchased_themes, $user_id);

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
