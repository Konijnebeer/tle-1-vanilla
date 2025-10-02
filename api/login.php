<?php

// required when working with sessions
session_start();

require_once('includes/database.php');

$login = false;
// Is user logged in?

if (isset($_POST['submit'])) {

    // Get form data
    $email = $_POST['email'];
    $password = $_POST['password_hash'];

    // Server-side validation
    $errors = [];

    if ($email == '') {
        $errors['email'] = 'email cannot be empty';
    }

    if ($password == '') {
        $errors['password'] = 'Please fill in a password';
    }

    // If data valid
    if (empty($errors)) {
        $db = Database::getInstance();
        $user = $db->fetch("SELECT password_hash, username, id FROM users WHERE email = ?", [$email]);

        // Check if user exists first, then verify password
        if ($user && password_verify($password, $user['password_hash'])) {

            // Get all groups the user is part of
            $groups = $db->fetchAll("SELECT g.id FROM `groups` g INNER JOIN user_group ug ON g.id = ug.group_id WHERE ug.user_id = ?", [$user['id']]);
            $groupIds = array_column($groups, 'id');

            // Store comprehensive user data in session
            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $email,
                'groups' => $groupIds
            ];

            // Redirect to secure page
            header('Location: ../home.html');
            exit();
        } else {
            // Credentials not valid or user doesn't exist
            $errors['loginFailed'] = 'Email/password incorrect';
        }
        //error incorrect log in
    } else {
        // User doesn't exist
        $errors['loginFailed'] = 'Email/password incorrect';
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="../styles/output.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Audiowide&display=swap" rel="stylesheet">
    <script type="text/javascript" src="../js/hex-background.js"></script>
    <title>ENA - Inloggen</title>
</head>

<body class="bg-[#E0A054] min-h-screen">

    <div class="hexagon hex-large-one"></div>
    <div class="hexagon hex-large-two"></div>
    <div class="hexagon hex-medium-one"></div>
    <div class="hexagon hex-small-one"></div>
    <div class="hexagon hex-small-two"></div>

    <header class="header justify-around">
        <h1 class="header-text">Inloggen</h1>
    </header>
    <main class="main center-screen">
        <div class="card">


            <form action="" method="post" class="space-y-6">
                <div>
                    <label class="label" for="email">Email
                        <input class="input"
                            id="email" type="text" name="email" value="<?= htmlentities($email ?? '') ?>" />
                    </label>
                    <?php if (isset($errors['email'])) { ?>
                        <p class="text-red-600 text-sm mt-1"><?= $errors['email'] ?></p>
                    <?php } ?>
                </div>

                <div>
                    <label class="label" for="password_hash">Wachtwoord
                        <input class="input"
                            id="password_hash" type="password" name="password_hash" />
                        <?php if (isset($errors['password'])) { ?>
                    </label>
                    <p class="text-red-600 text-sm mt-1"><?= $errors['password'] ?></p>
                <?php } ?>
                </div>

                <?php if (isset($errors['loginFailed'])) { ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <?= $errors['loginFailed'] ?>
                    </div>
                <?php } ?>

                <button class="button  font-semibold bg-[#87A4B7]"
                    type="submit" name="submit">Log in
                </button>
            </form>

            <div class="mt-6 flex flex-col gap-4">
                <a href="../createaccount.html"
                    class="button bg-sucsses">Maak een account</a>
                <a href="../start2.html"
                    class="button bg-secondary">&laquo;
                    Terug naar start</a>
            </div>
        </div>
    </main>
    <footer class="footer">
        <p>Copyright 2025 - TLE1 Team 3</p>
    </footer>
    <div class="wave-bottom fixed! bottom-0"></div>
    <div class="wave-accent fixed! bottom-0"></div>
</body>

</html>