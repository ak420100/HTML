<?php
$filename = 'friends.json';

function getFriends() {
    global $filename;
    if (file_exists($filename)) {
        $friends = json_decode(file_get_contents($filename), true);
    } else {
        $friends = [];
    }
    return $friends;
}

function saveFriends($friends) {
    global $filename;
    file_put_contents($filename, json_encode($friends));
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    header('Content-Type: application/json');
    echo json_encode(['friends' => getFriends()]);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $friends = getFriends();
    $newFriend = [
        'id' => uniqid(),
        'name' => $_POST['friendName'],
        'email' => $_POST['friendEmail'],
        'habits' => $_POST['friendHabits']
    ];
    $friends[] = $newFriend;
    saveFriends($friends);
    echo json_encode(['friends' => $friends]);
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $friends = getFriends();
    $friends = array_filter($friends, function($friend) {
        return $friend['id'] !== $_GET['id'];
    });
    saveFriends($friends);
    echo json_encode(['friends' => $friends]);
}
?>