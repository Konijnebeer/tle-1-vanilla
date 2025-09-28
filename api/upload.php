<?php
require_once './utils/response.php';
require_once './includes/database.php';

try {
    // Get folder parameter from URL, default to 'images'
    $folder = $_GET['folder'] ?? 'images';

    // Check if file was uploaded
    if (!isset($_FILES['file'])) {
        sendError('No file was selected', 400);
    }

    $file = $_FILES['file'];

    // Check for upload errors
    switch ($file['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            sendError('File is too large. Maximum file size is 2MB', 400);
        case UPLOAD_ERR_PARTIAL:
            sendError('File upload was interrupted', 400);
        case UPLOAD_ERR_NO_FILE:
            sendError('No file was selected', 400);
        case UPLOAD_ERR_NO_TMP_DIR:
            sendError('Server error: temporary folder missing', 500);
        case UPLOAD_ERR_CANT_WRITE:
            sendError('Server error: cannot write file', 500);
        case UPLOAD_ERR_EXTENSION:
            sendError('File upload blocked by server extension', 400);
        default:
            sendError('Unknown upload error', 500);
    }

    // Additional file size check (2MB limit)
    $maxFileSize = 2 * 1024 * 1024; // 2MB in bytes
    if ($file['size'] > $maxFileSize) {
        sendError('File is too large. Maximum file size is 2MB', 400);
    }
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    // Validate file type
    if (!in_array($file['type'], $allowedTypes)) {
        sendError('Invalid file type. Only JPG, PNG, WebP and GIF are allowed', 400);
    }

    // Get file extension
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($fileExtension, $allowedExtensions)) {
        sendError('Invalid file extension', 400);
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
            sendError('Failed to create upload directory', 500);
        }
    }

    // Create filename with UUID
    $fileName = $uuid . '.' . $fileExtension;
    $filePath = $uploadDir . $fileName;

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $filePath)) {
        sendError('Failed to move uploaded file', 500);
    }

    // Save to database using new database methods
    $db = Database::getInstance();

    try {
        $insertedId = $db->createRecord(
            "INSERT INTO images (id, path, name, extension, posted) VALUES (?, ?, ?, ?, ?)",
            [
                $uuid,
                'images/' . $folder . '/' . $fileName,
                $file['name'],
                $fileExtension,
                0
            ]
        );

        // createRecord returns the ID, so if we get here without exception, it worked

    } catch (Exception $e) {
        sendError('Failed to save image to database: ' . $e->getMessage(), 500);
    }

    // Return success response using response utility
    sendSuccess([
        'uuid' => $uuid,
        'filename' => $fileName,
        'folder' => $folder,
        'original_name' => $file['name']
    ]);
} catch (Exception $e) {
    sendError('Upload failed: ' . $e->getMessage(), 500);
} catch (Error $e) {
    sendError('PHP Error: ' . $e->getMessage(), 500);
}
