<?php
require_once './utils/response.php';
require_once './includes/database.php';
require_once './utils/acount.php';

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {

    // Handle JSON data from PUT request
    $input = json_decode(file_get_contents('php://input'), true);

    $groupName = $input['groupName'] ?? '';
    $description = $input['description'] ?? '';
    $theme = $input['theme'] ?? '';
    $user_id = getCurrentUserId();

    // Set empty error array
    $errors = [];

    // Get database instance
    $db = Database::getInstance();

    if (empty($groupName)) {
        $errors['groupName'] = "Groepsnaam mag niet leeg zijn";
//    } else if ($db->recordExists("SELECT id FROM `groups` WHERE name = ?", [$groupName])) {
//        $errors['groupName'] = "Een groep met deze naam bestaat al";
    }

    if(empty(($description))) {
        $errors['description'] = "Beschrijving mag niet leeg zijn";
    }

    if(empty(($theme))) {
        $errors['theme'] = "Thema mag niet leeg zijn";
    }

    if(empty($errors)){
        // Insert new group into database
        $db->createRecord("INSERT INTO `groups` (user_id ,name, discription, theme) VALUES (?, ?, ?, ?)", [$user_id ,$groupName, $description, $theme]);

        sendSuccess(['message' => 'Groep succesvol aangemaakt']);
    } else {
        sendError('Validatiefouten', 400, $errors);
    }
}