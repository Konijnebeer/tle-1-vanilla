<?php
require_once './utils/response.php';
require_once './includes/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    
    // Handle JSON data from PUT request
    $input = json_decode(file_get_contents('php://input'), true);
    
    $email = $input['email'] ?? '';
    $username = $input['username'] ?? '';
    $phoneNumber = $input['phoneNumber'] ?? '';
    $password = $input['password'] ?? '';
    $confirmPassword = $input['confirm_password'] ?? '';
    
    // Set empty error array
    $errors = [];

    // Get database instance
    $db = Database::getInstance();

    // EMAIL VALIDATION
    if (empty($email)) {
        $errors['email'] = "Email mag niet leeg zijn";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Email moet geldig zijn";
    } else if ($db->recordExists("SELECT id FROM users WHERE email = ?", [$email])) {
        $errors['email'] = "Een account met deze email bestaat al";
    }

    // USERNAME VALIDATION  
    if (empty($username)) {
        $errors['username'] = "Gebruikersnaam mag niet leeg zijn";
    } else if (!preg_match("/^[0-9A-Za-z]{6,16}$/", $username)) {
        $errors['username'] = "Gebruikersnaam moet 6-16 karakters lang zijn en mag alleen letters en cijfers bevatten";
    } else if ($db->recordExists("SELECT id FROM users WHERE username = ?", [$username])) {
        $errors['username'] = "Een account met deze gebruikersnaam bestaat al";
    }

    // PHONE NUMBER VALIDATION
    if (empty($phoneNumber)) {
        $errors['phoneNumber'] = "Telefoonnummer mag niet leeg zijn";
    } else {
        // Clean phone number: remove all non-digits except leading +
        $cleanPhone = preg_replace('/[^\d+]/', '', $phoneNumber);

        // Transform to uniform format
        if (preg_match('/^\+31(\d{9})$/', $cleanPhone, $matches)) {
            // Dutch format with country code: +31612345678
            $uniformPhone = '+31' . $matches[1];
        } else if (preg_match('/^0(\d{9})$/', $cleanPhone, $matches)) {
            // Dutch format without country code: 0612345678 -> +31612345678
            $uniformPhone = '+31' . $matches[1];
        } else if (preg_match('/^(\d{9})$/', $cleanPhone, $matches)) {
            // Dutch mobile without leading 0: 612345678 -> +31612345678
            $uniformPhone = '+31' . $matches[1];
        } else {
            $errors['phoneNumber'] = "Telefoonnummer moet een geldig Nederlands nummer zijn (bijv. 06-12345678 of +31612345678)";
        }

        // Check for duplicates with uniform format
        if (!isset($errors['phoneNumber'])) {
            if ($db->recordExists("SELECT id FROM users WHERE phone_number = ?", [$uniformPhone])) {
                $errors['phoneNumber'] = "Een account met dit telefoonnummer bestaat al";
            } else {
                // Update the phoneNumber variable to save uniform format
                $phoneNumber = $uniformPhone;
            }
        }
    }

    // PASSWORD VALIDATION
    if (empty($password)) {
        $errors['password'] = "Wachtwoord mag niet leeg zijn";
    } else if (!preg_match("/^(?=.*?[0-9])(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[^0-9A-Za-z]).{8,32}$/", $password)) {
        $errors['password'] = "Wachtwoord moet 8-32 karakters lang zijn, minimaal een hoofdletter, cijfer en speciaal teken bevatten";
    }

    // CONFIRM PASSWORD VALIDATION
    if (empty($confirmPassword)) {
        $errors['confirm_password'] = "Bevestig wachtwoord mag niet leeg zijn";
    } else if ($password !== $confirmPassword) {
        $errors['confirm_password'] = "Wachtwoorden komen niet overeen";
    }

    if (empty($errors)) {
        // Hash password
        $securePassword = password_hash($password, PASSWORD_DEFAULT);

        // Add the new user to the database
        $result = $db->createRecord(
            "INSERT INTO users (email, username, phone_number, password_hash) VALUES (?, ?, ?, ?)",
            [$email, $username, $phoneNumber, $securePassword]
        );

        if ($result) {
            session_start();

            // Get the just created user data
            $user = $db->getRow("SELECT password_hash, username, id FROM users WHERE id = ?", [$result]);

            // Get the groups the user is in
            $groups = $db->getRows("SELECT g.id FROM `groups` g INNER JOIN user_group ug ON g.id = ug.group_id WHERE ug.user_id = ?", [$user['id']]);
            $groupIds = array_column($groups, 'id');

            // Store comprehensive user data in session
            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $email,
                'groups' => $groupIds
            ];

            sendCreated(['message' => 'Account aangemaakt', 'user_id' => $result]);
        } else {
            sendError('Account aanmaken mislukt');
        }
    } else {
        sendValidationError($errors);
    }
}
