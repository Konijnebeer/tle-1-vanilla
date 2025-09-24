<?php

// required when working with sessions
session_start();

require_once('includes/database.php');

$login = false;
// Is user logged in?

if (isset($_POST['submit'])) {

    // Get form data
    $username = $_POST['username'];
    $password = $_POST['password_hash'];

    // Server-side validation
    $errors = [];

    if ($username == '') {
        $errors['username'] = 'Username cannot be empty';
    }

    if ($password == '') {
        $errors['password'] = 'Please fill in a password';
    }

    // If data valid
    if (empty($errors)) {
        $db = Database::getInstance();
        $user = $db->fetch("SELECT password_hash FROM users WHERE email = ?", [$username]);
//        $data = ['users' => $user];
//        print_r($data);
//
        // Check if the provided password matches the stored password in the database
        if (password_verify($password, $user['password_hash'])) {
//                echo 'password correct';
//                exit;

            // Store the user in the session
            $_SESSION['user'] = $username;

            // Redirect to secure page
            header('Location: profiel.php');
            exit();
        } else {
            // Credentials not valid
            $errors['loginFailed'] = 'Username/password incorrect';
        }
        //error incorrect log in
    } else {
        // User doesn't exist
        $errors['loginFailed'] = 'Username/password incorrect';
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@1.0.2/css/bulma.min.css">
    <link rel="stylesheet" href="../styles/login.css">

    <title>Log in</title>
</head>
<body>
<section class="section">
    <div class="container">
        <h2 class="title">Log in</h2>

        <?php if ($login) { ?>
            <p>Je bent ingelogd!</p>
            <p><a href="logout.php">Uitloggen</a></p>
        <?php } else { ?>

            <section class="columns">
                <form class="column" action="" method="post">

                    <div class="field">
                        <div class="field-label">
                            <label class="label" for="email">Email</label>
                        </div>
                        <div class="field-body">
                            <div class="field">
                                <div class="control has-icons-left">
                                    <input class="input" id="username" type="text" name="username"
                                           value="<?= htmlentities($username ?? '') ?>"/>
                                    <span class="icon is-small is-left"><i class="fas fa-envelope"></i></span>
                                </div>
                                <p class="help is-danger">
                                    <?= $errors['username'] ?? '' ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="field">
                        <div class="field-label">
                            <label class="label" for="password">Password</label>
                        </div>
                        <div class="field-body">
                            <div class="field">
                                <div class="control has-icons-left">
                                    <input class="input" id="password_hash" type="password" name="password_hash"/>
                                    <span class="icon is-small is-left"><i class="fas fa-lock"></i></span>

                                    <?php if (isset($errors['loginFailed'])) { ?>
                                        <div class="notification">
                                            <button class="delete"></button>
                                            <?= $errors['loginFailed'] ?>
                                        </div>
                                    <?php } ?>

                                </div>
                                <p class="help">
                                    <?= $errors['password'] ?? '' ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="field">
                        <div class="field-label"></div>
                        <div class="field-body">
                            <button class="button" type="submit" name="submit">Log
                                in
                            </button>
                        </div>
                    </div>

                    <a class="button" href="../start.html">&laquo; Go back to START</a>
                </form>
            </section>

        <?php } ?>

    </div>
</section>
</body>
</html>