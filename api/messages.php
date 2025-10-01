<?php
require_once './includes/database.php';
require_once './utils/response.php';
require_once './utils/acount.php';


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $user_id = getCurrentUserId();
    if (!empty($_GET['user'])) {
        $user2_id = (int)$_GET['user'];
    } else {
        sendError('User ID is required', 400);
        exit;
    }

    if (!$user_id) {
        sendError('Not logged in', 401);
        exit;
    }

    $db = Database::getInstance();
    $results = $db->getRows(
        "SELECT m.*, 
                sender.username AS sender_name, 
                receiver.username AS receiver_name
         FROM messages m
         JOIN users sender ON m.sender_id = sender.id
         JOIN users receiver ON m.receiver_id = receiver.id
         WHERE (m.sender_id = ? AND m.receiver_id = ?)
            OR (m.sender_id = ? AND m.receiver_id = ?)
         ORDER BY m.sent_at ASC", [
            $user_id, $user2_id, $user2_id, $user_id
        ]
    );

    sendSuccess($results);
}