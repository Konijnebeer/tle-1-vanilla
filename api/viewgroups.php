<?php
require_once './utils/acount.php';
require_once './includes/database.php';
require_once './utils/response.php';

// Check authentication using the utility function
requireAuth();

// Get user ID from session
$userId = getCurrentUserId();

try {
    $action = $_GET['action'] ?? '';

    if ($action === 'getall') {
        // Get user from session
        $user = getCurrentUser();

        if (empty($user['groups'])) {
            sendSuccess(['posts' => []]);
            return;
        }

        // Get database instance
        $db = Database::getInstance();

        // Fixed SQL query with backticks and correct column name
        $groups = $db->getRows(
            'SELECT name, discription, theme FROM `groups` WHERE `groups`.user_id = ?',
            [$userId]
        );

        sendSuccess(['groups' => $groups]);
    } else {
        sendError('Invalid action', 400);
    }

} catch (Exception $e) {
    sendError('Database error: ' . $e->getMessage());
}