<?php
require_once './utils/acount.php';
require_once './includes/database.php';
require_once './utils/response.php';

requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Method not allowed', 405);
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $groupId = $input['group_id'] ?? null;
    $email = $input['email'] ?? null;

    // Validation
    if (!$groupId || !$email) {
        sendError('Groep ID en e-mail zijn verplicht', 400);
    }

    $db = Database::getInstance();
    $currentUserId = getCurrentUserId();

    // Check permission
    $isMember = $db->getRow(
        "SELECT 1 FROM user_group WHERE user_id = ? AND group_id = ?",
        [$currentUserId, $groupId]
    );

    if (!$isMember) {
        sendError('Geen toegang tot deze groep', 403);
    }

    // Find user by email
    $invitedUser = $db->getRow("SELECT id FROM users WHERE email = ?", [$email]);

    if (!$invitedUser) {
        sendError('Gebruiker niet gevonden', 404);
    }

    $invitedUserId = $invitedUser['id'];

    // Check if already in group
    $alreadyInGroup = $db->getRow(
        "SELECT 1 FROM user_group WHERE user_id = ? AND group_id = ?",
        [$invitedUserId, $groupId]
    );

    if ($alreadyInGroup) {
        sendError('Gebruiker is al lid van deze groep', 400);
    }

    // Add to group
    $db->createRecord(
        "INSERT INTO user_group (user_id, group_id) VALUES (?, ?)",
        [$invitedUserId, $groupId]
    );

    sendSuccess(['message' => 'Gebruiker succesvol toegevoegd aan de groep']);

} catch (Exception $e) {
    sendError('Database error: ' . $e->getMessage());
}