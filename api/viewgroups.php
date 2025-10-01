<?php
require_once './utils/acount.php';
require_once './includes/database.php';
require_once './utils/response.php';

requireAuth();

try {
    $action = $_GET['action'] ?? '';

    if ($action === 'getall') {
        $user = getCurrentUser();

        if (empty($user['groups'])) {
            sendSuccess(['groups' => []]);
            return;
        }

        $db = Database::getInstance();

        $placeholders = str_repeat('?,', count($user['groups']) - 1) . '?';

        $groups = $db->getRows(
            "SELECT id, name, discription, theme FROM `groups` WHERE id IN ($placeholders)",
            $user['groups']
        );

        sendSuccess(['groups' => $groups]);
    } else {
        sendError('Invalid action', 400);
    }

} catch (Exception $e) {
    sendError('Database error: ' . $e->getMessage());
}