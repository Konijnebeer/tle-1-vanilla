<?php
require_once './includes/database.php';
require_once './utils/response.php';
require_once './utils/acount.php';
// Wireframe. Tabel ophalen gebaseerd op user_id. Knopjes om naar messages toe te gaan.


if ($_SERVER['REQUEST_METHOD'] === 'GET') {


    $db = Database::getInstance();
    sendSuccess([
        [
            'id' => 1,
            'naam' => 'John Doe',
            'email' => 'john.doe@example.com',
            'telefoon' => '+1234567890'
        ]
    ]);
}