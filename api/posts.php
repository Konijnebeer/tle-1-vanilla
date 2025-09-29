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
    $action = $_GET['action'] ?? '';

    if ($action === 'getall') {
        // Get user from session
        $user = getCurrentUser();

        if (empty($user['groups'])) {
            sendSuccess(['posts' => []]);
            return;
        }

        // Get database connection
        $db = Database::getInstance();

        // Create placeholders for user's groups
        $placeholders = str_repeat('?,', count($user['groups']) - 1) . '?';

        // Updated SQL query with group names from database, ordered by newest first
        $sql = "
            SELECT 
                posts.id,
                users.username,
                posts.group_id,
                groups.name AS group_name,
                images.path AS image_path,
                posts.text_content,
                posts.created_at,
                images.name AS image_name
            FROM posts
            JOIN users ON posts.user_id = users.id
            JOIN `groups` ON posts.group_id = groups.id
            LEFT JOIN images ON posts.image_id = images.id
            WHERE posts.group_id IN ($placeholders)
            ORDER BY posts.created_at DESC
        ";

        $posts = $db->getRows($sql, $user['groups']);

        sendSuccess(['posts' => $posts]);
    } else {
        sendError('Unknown action', 400);
    }
} catch (Exception $e) {
    sendError('Failed to fetch posts: ' . $e->getMessage(), 500);
}
