<?php

/**
 * Database Class Usage Examples
 * 
 * This file demonstrates how to use the improved Database class methods.
 * Each method has a clear purpose and returns predictable data types.
 */

require_once 'database.php';

// Get database instance
$db = Database::getInstance();

// =============================================================================
// DATA RETRIEVAL EXAMPLES
// =============================================================================

// Get a single user by email
$user = $db->getRow("SELECT id, username, email FROM users WHERE email = ?", ['user@example.com']);
if ($user) {
    echo "Found user: " . $user['username'];
} else {
    echo "User not found";
}

// Get multiple posts for a user
$posts = $db->getRows("SELECT title, content, created_at FROM posts WHERE user_id = ?", [123]);
foreach ($posts as $post) {
    echo $post['title'] . "\n";
}

// Get a count of total users
$userCount = $db->getValue("SELECT COUNT(*) FROM users");
echo "Total users: " . $userCount;

// Get user by ID (convenience method)
$user = $db->getById('users', 123, 'username, email, created_at');

// =============================================================================
// DATA MODIFICATION EXAMPLES
// =============================================================================

// Create a new user (returns the new user's ID)
$newUserId = $db->createRecord(
    "INSERT INTO users (email, username, password_hash) VALUES (?, ?, ?)",
    ['new@user.com', 'newuser', password_hash('password', PASSWORD_DEFAULT)]
);
echo "New user ID: " . $newUserId;

// Update user information (returns number of affected rows)
$updatedRows = $db->updateRecords(
    "UPDATE users SET username = ? WHERE id = ?",
    ['updated_username', 123]
);
echo "Updated {$updatedRows} user(s)";

// Delete old posts (returns number of deleted rows)
$deletedRows = $db->deleteRecords(
    "DELETE FROM posts WHERE created_at < ?",
    ['2024-01-01']
);
echo "Deleted {$deletedRows} old posts";

// =============================================================================
// UTILITY EXAMPLES
// =============================================================================

// Check if email exists
$emailExists = $db->recordExists("SELECT 1 FROM users WHERE email = ?", ['test@example.com']);
if ($emailExists) {
    echo "Email already taken";
}

// Count posts by user
$postCount = $db->countRecords('posts', 'user_id = ?', [123]);
echo "User has {$postCount} posts";

// Count all users (no WHERE clause)
$totalUsers = $db->countRecords('users');
echo "Total users: {$totalUsers}";

// =============================================================================
// TRANSACTION EXAMPLE
// =============================================================================

try {
    $db->beginTransaction();

    // Create user
    $userId = $db->createRecord(
        "INSERT INTO users (email, username, password_hash) VALUES (?, ?, ?)",
        ['transaction@test.com', 'transactionuser', password_hash('password', PASSWORD_DEFAULT)]
    );

    // Create user's first post
    $postId = $db->createRecord(
        "INSERT INTO posts (user_id, title, content) VALUES (?, ?, ?)",
        [$userId, 'Welcome Post', 'This is my first post!']
    );

    $db->commit();
    echo "User and post created successfully";
} catch (Exception $e) {
    $db->rollback();
    echo "Transaction failed: " . $e->getMessage();
}

// =============================================================================
// MIGRATION FROM OLD METHODS (for reference)
// =============================================================================

// OLD WAY (deprecated):
// $user = $db->fetch("SELECT * FROM users WHERE id = ?", [123]);
// $posts = $db->fetchAll("SELECT * FROM posts WHERE user_id = ?", [123]);
// $newId = $db->insert("INSERT INTO users (...) VALUES (...)", [...]);

// NEW WAY (recommended):
// $user = $db->getRow("SELECT * FROM users WHERE id = ?", [123]);
// $posts = $db->getRows("SELECT * FROM posts WHERE user_id = ?", [123]);
// $newId = $db->createRecord("INSERT INTO users (...) VALUES (...)", [...]);
