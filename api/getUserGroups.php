<?php
require_once './utils/response.php';
require_once './utils/acount.php';
require_once './includes/database.php';

// Only allow GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendError('Method not allowed', 405);
}

// Check authentication
requireAuth();

try {
    // Get user from session
    $user = getCurrentUser();

    if (empty($user['groups'])) {
        sendSuccess([]);
        return;
    }

    // Get database connection
    $db = Database::getInstance();

    // Get group details for user's groups
    $placeholders = str_repeat('?,', count($user['groups']) - 1) . '?';
    $groups = $db->getRows(
        "SELECT id, name FROM `groups` WHERE id IN ($placeholders) ORDER BY name",
        $user['groups']
    );

    sendSuccess($groups);
} catch (Exception $e) {
    sendError('Failed to get user groups: ' . $e->getMessage(), 500);
}
