<?php
require_once './utils/acount.php';
require_once './includes/database.php';
require_once './utils/response.php';

// Check authentication using the utility function
requireAuth();

// Get user ID from session
$userId = getCurrentUserId();

try {
    // Get database instance
    $db = Database::getInstance();

    // Fetch profile data
    $profiel = $db->getRow(
        'SELECT email, username, phone_number, created_at, updated_at FROM users WHERE id = ?',
        [$userId]
    );

    if ($profiel) {
        sendSuccess($profiel); // Use the response utility
    } else {
        sendNotFound('Geen profielgegevens gevonden');
    }
} catch (Exception $e) {
    sendError('Database error: ' . $e->getMessage());
}
