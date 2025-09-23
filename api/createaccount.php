<?php
/** @var $db mysqli */
if (isset($_POST['email'])) {
    require_once('../includes/database.php'); // adjust path if needed

    $email = mysqli_real_escape_string($db, $_POST['email']);
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $phoneNumber = mysqli_real_escape_string($db, $_POST['phoneNumber']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    $errors = [];

    if ($password !== $confirmPassword) {
        $errors['confirm_password'] = "Wachtwoorden komen niet overeen";
    }

    if (empty($errors)) {
        $securePassword = password_hash($password, PASSWORD_DEFAULT);

        $query = "INSERT INTO users (email, username, phone_number, password_hash)
                  VALUES ('$email', '$username', '$phoneNumber', '$securePassword')";

        $result = mysqli_query($db, $query);

        if ($result) {
            mysqli_close($db);
            header('Location: ../start.html');
            exit();
        } else {
            die('Error: ' . mysqli_error($db));
        }
    } else {
        echo json_encode(['errors' => $errors]);
        exit();
    }
}
