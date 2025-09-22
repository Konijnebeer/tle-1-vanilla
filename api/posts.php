<?php

require_once 'includes/database.php';
if (isset($_GET['action'])) {
    $action = $_GET['action'];

    if ($action === 'getall') {
        try {
            $db = Database::getInstance();
            $users = $db->fetchAll("SELECT * FROM users");
            $data = ['users' => $users];
        } catch (Exception $e) {
            $data = ['error' => 'Failed to fetch users'];
        }
    } else {
        $data = ['error' => 'Unknown action'];
    }
} else {
    $data = ['error' => 'Action not specified'];
}

// Set the header & output JSON so the client will know what to expect.
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");
echo json_encode($data);
exit;
