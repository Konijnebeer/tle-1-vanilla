<?php
require_once 'includes/database.php';

header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
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

    // Validate required fields
    if (empty($data['title'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Title is required']);
        exit;
    }

    if (empty($data['caption'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Caption is required']);
        exit;
    }

    $title = trim($data['title']);
    $caption = trim($data['caption']);
    $imageUuid = isset($data['image_uuid']) ? $data['image_uuid'] : null;

    // Debug: Log the processed values
    error_log("Title: $title, Caption: $caption, Image UUID: " . ($imageUuid ?? 'null'));

    // For now, we'll use hardcoded values for user_id and group_id
    // In a real application, you would get these from session/authentication
    $userId = 1; // Hardcoded user ID - replace with actual user authentication
    $groupId = 1; // Hardcoded group ID - replace with actual group selection

    // Get database connection
    $db = Database::getInstance();
    $conn = $db->getConnection();

    // Debug: Check if connection is valid
    if (!$conn) {
        throw new Exception('Database connection failed');
    }

    // Start a transaction
    $conn->beginTransaction();

    try {
        // Insert the post
        $stmt = $conn->prepare("
            INSERT INTO posts (user_id, group_id, image_id, text_content) 
            VALUES (?, ?, ?, ?)
        ");

        $success = $stmt->execute([
            $userId,
            $groupId,
            $imageUuid,
            $caption
        ]);

        if (!$success) {
            throw new Exception('Failed to create post');
        }

        $postId = $conn->lastInsertId();

        // If an image was uploaded, update the images table to mark it as posted
        if ($imageUuid) {
            $imageStmt = $conn->prepare("UPDATE images SET posted = 1 WHERE id = ?");
            $imageSuccess = $imageStmt->execute([$imageUuid]);

            if (!$imageSuccess) {
                throw new Exception('Failed to update image status');
            }
        }

        // Commit the transaction
        $conn->commit();

        // Return success response
        echo json_encode([
            'success' => true,
            'post_id' => $postId,
            'message' => 'Post created successfully'
        ]);
    } catch (Exception $e) {
        // Rollback the transaction on error
        $conn->rollBack();
        throw $e;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to create post: ' . $e->getMessage()]);
} catch (Error $e) {
    http_response_code(500);
    echo json_encode(['error' => 'PHP Error: ' . $e->getMessage()]);
}
