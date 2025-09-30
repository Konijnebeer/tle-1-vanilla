<?php
require_once './includes/database.php';
require_once './utils/response.php';
require_once './utils/acount.php';


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $user_id = getCurrentUserId();

    if (!$user_id) {
        sendError('Not logged in', 401);
        exit;
    }

    $db = Database::getInstance();

    $results = $db->getRows(
        "SELECT u.id, u.username, u.email, u.phone_number
         FROM friendships f
         JOIN users u ON (
             (f.user1_id = :user_id_1 AND f.user2_id = u.id) OR
             (f.user2_id = :user_id_2 AND f.user1_id = u.id)
         )
         WHERE f.status = 'ACCEPTED' AND u.id != :user_id_3",
        [
            'user_id_1' => $user_id,
            'user_id_2' => $user_id,
            'user_id_3' => $user_id
        ]
    );


    sendSuccess($results);
}