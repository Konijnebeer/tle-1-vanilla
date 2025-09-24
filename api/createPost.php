<?php
session_start();
require_once 'includes/database.php';

header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

try {
    // Get the JSON input
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    // Check if JSON parsing failed
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON: ' . json_last_error_msg());
    }

    // Validate caption (allow empty captions)
    $caption = isset($data['caption']) ? trim($data['caption']) : '';
    $imageUuid = isset($data['image_uuid']) ? $data['image_uuid'] : null;

    // Get user info from session
    $userId = $_SESSION['user']['id'];
    
    // For now, use the first group the user belongs to, or null if no groups
    $groupId = !empty($_SESSION['user']['groups']) ? $_SESSION['user']['groups'][0] : null;

    // Get database connection
    $db = Database::getInstance();
    
    // Insert the post
    $postId = $db->insert("
        INSERT INTO posts (user_id, group_id, image_id, text_content, created_at) 
        VALUES (?, ?, ?, ?, NOW())
    ", [
        $userId,
        $groupId,
        $imageUuid,
        $caption
    ]);

    // If an image was uploaded, update the images table to mark it as posted
    if ($imageUuid) {
        $db->query("UPDATE images SET posted = 1 WHERE id = ?", [$imageUuid]);
    }

    // Return success response
    echo json_encode([
        'success' => true,
        'post_id' => $postId,
        'message' => 'Post created successfully'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to create post: ' . $e->getMessage()
    ]);
}