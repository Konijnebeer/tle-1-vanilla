<?php

/**
 * User Account Authentication Check
 * Simple utility to check if user is logged in
 */

// session_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'response.php';

/**
 * Check if user is authenticated
 * @return bool True if user is logged in, false otherwise
 */
function isUserLoggedIn()
{
    return isset($_SESSION['user']) && !empty($_SESSION['user']['id']);
}

/**
 * Get current user data
 * @return array|null User data if logged in, null otherwise
 */
function getCurrentUser()
{
    return isUserLoggedIn() ? $_SESSION['user'] : null;
}

/**
 * Require authentication - send 401 if not logged in
 * Use this in API endpoints that need authentication
 */
function requireAuth()
{
    if (!isUserLoggedIn()) {
        sendUnauthorized('Please log in to access this resource');
    }
}

/**
 * Get user ID if logged in
 * @return int|null User ID or null if not logged in
 */
function getCurrentUserId()
{
    $user = getCurrentUser();
    return $user ? (int)$user['id'] : null;
}

/**
 * Check if user belongs to a specific group
 * @param int $groupId The group ID to check
 * @return bool True if user is in the group
 */
function userHasGroup($groupId)
{
    $user = getCurrentUser();
    if (!$user || !isset($user['groups'])) {
        return false;
    }

    return in_array($groupId, $user['groups']);
}

// Handle direct API requests to this file
if ($_SERVER['REQUEST_METHOD'] === 'GET' /* && basename($_SERVER['SCRIPT_NAME']) === 'acount.php' */ && isset($_GET['type']) && $_GET['type'] === 'user') {
    $user = getCurrentUser();

    if ($user) {
        // User is logged in - return user data
        sendSuccess([
            'logged_in' => true,
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'groups' => $user['groups'] ?? []
            ]
        ]);
    } else {
        // User is not logged in
        sendUnauthorized('Not logged in');
    }
}
