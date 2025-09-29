<?php

/**
 * Example Usage of Response Utilities
 * This file shows how to use the new response functions in your API endpoints
 */

require_once 'utils/response.php';
require_once 'utils/acount.php';

// Example 1: Simple success response
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'test') {
    $data = ['message' => 'Hello World', 'timestamp' => time()];
    sendSuccess($data);
}

// Example 2: Protected endpoint that requires authentication
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'protected') {
    requireAuth(); // This will send 401 if user is not logged in

    $user = getCurrentUser();
    sendSuccess([
        'message' => 'Welcome to protected area',
        'user' => $user['username']
    ]);
}

// Example 3: Validation error example
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['action'] === 'validate') {
    $errors = [];

    if (empty($_POST['name'])) {
        $errors['name'] = 'Name is required';
    }

    if (empty($_POST['email'])) {
        $errors['email'] = 'Email is required';
    }

    if (!empty($errors)) {
        sendValidationError($errors);
    }

    sendSuccess(['message' => 'Validation passed']);
}

// Example 4: Not found example
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'user') {
    $userId = $_GET['id'] ?? null;

    if (!$userId) {
        sendError('User ID is required', 400);
    }

    // Simulate checking database
    if ($userId != '123') {
        sendNotFound('User not found');
    }

    sendSuccess(['id' => $userId, 'name' => 'Test User']);
}

// Example 5: Create new record
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['action'] === 'create') {
    // Simulate creating a record
    $newId = rand(1000, 9999);

    sendCreated([
        'id' => $newId,
        'message' => 'Record created successfully'
    ]);
}

// Default: Method not allowed
sendError('Invalid request', 405);
