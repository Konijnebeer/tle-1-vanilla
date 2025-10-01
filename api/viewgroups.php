<?php
require_once './utils/acount.php';
require_once './includes/database.php';
require_once './utils/response.php';

// Check authentication using the utility function
requireAuth();

try {
    $action = $_GET['action'] ?? '';

    if ($action === 'getall') {
        // Get user from session (same as posts.php)
        $user = getCurrentUser();

        if (empty($user['groups'])) {
            sendSuccess(['groups' => []]); // Changed from 'posts' to 'groups'
            return;
        }

        // Get database instance
        $db = Database::getInstance();

        // Create placeholders for user's groups (same as posts.php)
        $placeholders = str_repeat('?,', count($user['groups']) - 1) . '?';

        // Get groups that the user is member of, not groups they own
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