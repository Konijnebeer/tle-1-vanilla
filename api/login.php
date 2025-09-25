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
    //error incorrect log in

    //    }
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
    <title>Log in</title>
</head>

<body class="bg-[#E0A054] min-h-screen">

    <div class="hexagon hex-large-one"></div>
    <div class="hexagon hex-large-two"></div>
    <div class="hexagon hex-medium-one"></div>
    <div class="hexagon hex-small-one"></div>
    <div class="hexagon hex-small-two"></div>

    <div class="min-h-screen flex flex-col items-center justify-center px-4">
        <div class="bg-[#B18A5E] p-8 rounded-xl shadow-lg w-full max-w-md">
            <h2 class="text-2xl font-bold text-center mb-6 font-audiowide">Log in</h2>

            <?php if ($login) { ?>
                <div class="text-center space-y-4">
                    <p class="text-green-700 font-semibold">Je bent ingelogd!</p>
                    <a href="logout.php" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg shadow-md transition transform hover:scale-105 inline-block">Uitloggen</a>
                </div>
            <?php } else { ?>

                <form action="" method="post" class="space-y-6">
                    <div>
                        <label class="block text-gray-800 font-semibold mb-2" for="email">Email</label>
                        <input class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500"
                            id="email" type="text" name="email" value="<?= htmlentities($email ?? '') ?>" />
                        <?php if (isset($errors['email'])) { ?>
                            <p class="text-red-600 text-sm mt-1"><?= $errors['email'] ?></p>
                        <?php } ?>
                    </div>

                    <div>
                        <label class="block text-gray-800 font-semibold mb-2" for="password_hash">Password</label>
                        <input class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500"
                            id="password_hash" type="password" name="password_hash" />
                        <?php if (isset($errors['password'])) { ?>
                            <p class="text-red-600 text-sm mt-1"><?= $errors['password'] ?></p>
                        <?php } ?>
                    </div>

                    <?php if (isset($errors['loginFailed'])) { ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            <?= $errors['loginFailed'] ?>
                        </div>
                    <?php } ?>

                    <button class="w-full bg-gray-700 hover:bg-gray-800 text-white px-6 py-3 rounded-lg shadow-md transition transform hover:scale-105"
                        type="submit" name="submit">Log in</button>
                </form>

                <div class="mt-6 space-y-3 text-center">
                    <a href="../createaccount.html" class="block bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg shadow-md transition transform hover:scale-105">Create Account</a>
                    <a href="../start.html" class="block bg-[#8B7355] hover:bg-gray-600 text-white px-6 py-3 rounded-lg shadow-md transition transform hover:scale-105">&laquo; Go back to START</a>
                </div>

            <?php } ?>
        </div>
    </div>
</body>

</html>