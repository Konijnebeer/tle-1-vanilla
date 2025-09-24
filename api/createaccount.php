<?php
/** @var $db mysqli *///
if (isset($_POST['email'])) {
    
    $email = $_POST['email'];
    $username = $_POST['username'];
    $phoneNumber = $_POST['phoneNumber'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    
    $errors = [];
    
    if ($password !== $confirmPassword) {
        $errors['confirm_password'] = "Wachtwoorden komen niet overeen";
    }
    
    if (empty($errors)) {
        require_once('./includes/database.php'); // adjust path if needed
        $securePassword = password_hash($password, PASSWORD_DEFAULT);

        $ // Get database connection
        $db = Database::getInstance();

        // Insert the post
        $result = $db->insert("INSERT INTO users (email, username, phone_number, password_hash)VALUES (?, ?, ?, ?)", [$email, $username, $phoneNumber, $securePassword]);

        if ($result) {
            header('Location: ../start.html');
            exit();
        } else {
            die('Error: ' . 'something went horribly wrong');
        }
    } else {
        echo json_encode(['errors' => $errors]);
        exit();
    }
}
