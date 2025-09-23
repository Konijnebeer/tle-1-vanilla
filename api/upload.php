<?php
require_once 'includes/database.php';

// Enable error reporting for debugging
if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

header('Content-Type: application/json');

try {
    // Get folder parameter from URL, default to 'images'
    $folder = $_GET['folder'] ?? 'images';

    // Check if file was uploaded
    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo json_encode(['error' => 'No file uploaded or upload error']);
        exit;
    }

    $file = $_FILES['file'];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    // Validate file type
    if (!in_array($file['type'], $allowedTypes)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid file type. Only JPG, PNG, WebP and GIF are allowed']);
        exit;
    }

    // Get file extension
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($fileExtension, $allowedExtensions)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid file extension']);
        exit;
    }

    // Normalize extension for database
    if ($fileExtension === 'jpeg') {
        $fileExtension = 'jpg';
    }

    // Generate UUID for the image
    $uuid = sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff)
    );

    // Create upload directory based on folder parameter
    $uploadDir = '../images/' . $folder . '/';
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            throw new Exception('Failed to create upload directory');
        }
    }

    // Create filename with UUID
    $fileName = $uuid . '.' . $fileExtension;
    $filePath = $uploadDir . $fileName;

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $filePath)) {
        throw new Exception('Failed to move uploaded file');
    }

    // Save to database
    $db = Database::getInstance();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("INSERT INTO images (id, path, name, extension, posted) VALUES (?, ?, ?, ?, ?)");
    $success = $stmt->execute([
        $uuid,
        'images/' . $folder . '/' . $fileName,
        $file['name'],
        $fileExtension,
        0 // Use 0 instead of false for boolean in database
    ]);

    if (!$success) {
        throw new Exception('Failed to save image to database');
    }

    // Return success response with UUID
    echo json_encode([
        'success' => true,
        'uuid' => $uuid,
        'filename' => $fileName,
        'folder' => $folder,
        'original_name' => $file['name']
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Upload failed: ' . $e->getMessage()]);
} catch (Error $e) {
    http_response_code(500);
    echo json_encode(['error' => 'PHP Error: ' . $e->getMessage()]);
}
