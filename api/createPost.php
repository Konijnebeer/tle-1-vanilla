<?php
require_once './utils/response.php';
require_once './utils/acount.php';
require_once './includes/database.php';

// Only allow PUT requests
if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    sendError('Method not allowed', 405);
}

// Check authentication using the utility function
requireAuth();

// Get user ID from session
$userId = getCurrentUserId();
$user = getCurrentUser();

try {
    // Get the JSON input
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    // Check if JSON parsing failed
    if (json_last_error() !== JSON_ERROR_NONE) {
        sendError('Invalid JSON: ' . json_last_error_msg(), 400);
    }

    // Validate caption (allow empty captions)
    $caption = isset($data['caption']) ? trim($data['caption']) : '';
    $imageUuid = isset($data['image_uuid']) ? $data['image_uuid'] : null;
    $selectedGroupId = isset($data['group_id']) ? (int)$data['group_id'] : null;

    // Validate that user belongs to the selected group
    if ($selectedGroupId && !userHasGroup($selectedGroupId)) {
        sendError('You are not a member of the selected group', 403);
    }

    // Use selected group or default to first group if none selected
    $groupId = $selectedGroupId ?: (!empty($user['groups']) ? $user['groups'][0] : null);

    // Get database connection
    $db = Database::getInstance();
    
    // Insert the post using new database method
    $postId = $db->createRecord("
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
        $db->updateRecords("UPDATE images SET posted = 1 WHERE id = ?", [$imageUuid]);
    }

    // Return success response
    sendCreated([
        'post_id' => $postId,
        'message' => 'Post created successfully'
    ]);

} catch (Exception $e) {
    sendError('Failed to create post: ' . $e->getMessage(), 500);
}