<?php
session_start();
header('Content-Type: application/json');

require_once './includes/database.php';

// Controleer of gebruiker is ingelogd
if (!isset($_SESSION['user'])) {
    echo json_encode(['error' => 'Niet ingelogd']);
    exit;
}

$userId = $_SESSION['user']['id'];

// Haal PDO via de Database
$db = Database::getInstance();
$pdo = $db->getConnection();

// Haal profielgegevens op
$profiel = $db->fetch('SELECT email, username, phone_number, created_at, updated_at FROM users WHERE id = ?', [$userId]);
// print_r($profiel);
// exit;

if ($profiel) {
    echo json_encode($profiel);
} else {
    echo json_encode(['error' => 'Geen profielgegevens gevonden']);
}
