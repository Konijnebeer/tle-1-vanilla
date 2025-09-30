<?php

require_once './utils/response.php';
require_once './utils/acount.php';
require_once './includes/database.php';
include_once './includes/env.php';


// Only allow GET requests
// if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
//     sendError('Method not allowed', 405);
// }


if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Check authentication
    requireAuth();

    // Handle JSON data from PUT request
    $input = json_decode(file_get_contents('php://input'), true);

    $email = $input['email'] ?? '';
    $groupId = (int)($input['group'] ?? '');

    // Get user Id of current user
    $userId = getCurrentUserId();
    // Set empty error array
    $errors = [];

    // Get database connection
    $db = Database::getInstance();

    // EMAIL VALIDATION
    if (empty($email)) {
        $errors['email'] = "Email mag niet leeg zijn";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Email moet geldig zijn";
    } else if ($db->recordExists("SELECT id FROM users WHERE email = ?", [$email])) {
        $errors['email'] = "Een account met deze email bestaat al";
    }

    // GROUP VALIDATION
    if (empty($groupId)) {
        $errors['group'] = "Groep mag niet leeg zijn";
    } else if (!$db->recordExists("SELECT id FROM `groups` WHERE id = ?", [$groupId])) {
        $errors['group'] = "Groep bestaat niet";
    } else if (!$db->recordExists("SELECT id FROM `groups` WHERE id = ? AND user_id = ?", [$groupId, $userId])) {
        $errors['group'] = "Je bent niet de eigenaar van deze groep";
    }
    if (empty($errors)) {
        try {

            $url = 'https://project.gmt.hr.nl/2025_2026/tle1_t3/invite.html';
            if (ENVIRONMENT === 'development') {
                $url = 'http://tle-1-vanilla.test/invite.html';
            }
            // Get current Time
            $time = time();
            // Encode the user Id and Group Id in base64 + the time
            $base64 = base64_encode("$userId/$groupId/$time");
            $url = $url . '?code=' . $base64;

            // Send email
            // Prepare email content
            $subject = 'Uitnodiging voor groep';
            $message = "
            <html>
            <head>
                <title>Uitnodiging</title>
            </head>
            <body>
                <p>Je bent uitgenodigd voor een groep. Klik op de knop om deel te nemen:</p>
                <a href='$url' style='display: inline-block; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px;'>Deelnemen</a>
            </body>
            </html>
            ";
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: noreply@gmt.hr.nl" . "\r\n";

            if (ENVIRONMENT === 'development') { 
                sendSuccess(['url' => $url]);
            }
            // Send email
            if (mail($email, $subject, $message, $headers)) {
                sendSuccess('Invite sent successfully');
            } else {
                sendError('Failed to send email', 500);
            }
        } catch (Exception $e) {
            sendError('Failed create invite: ' . $e->getMessage(), 500);
        }
    } else {
        sendValidationError($errors);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['code']) && !empty($_GET['code'])) {

        // Get all the info from the URL
        $info = base64_decode($_GET['code']);
        $parts = explode('/', $info);
        $userId = $parts[0];
        $groupId = $parts[1];
        $time = $parts[2];

        // Get the current time
        $currentTime = time();

        // Check if the invite is less than 1 week old
        $weekInSeconds = 7 * 24 * 60 * 60;
        if ($currentTime - $time > $weekInSeconds) {
            sendError('Invite has expired', 400);
            return;
        }

        try {
            // Get database connection
            $db = Database::getInstance();

            // Get username of user that sent invite
            $userName = $db->getRow("SELECT username FROM users WHERE id = ?", [$userId]);

            // SQL statement
            $sql = "
            SELECT
                posts.id,
                users.username,
                posts.group_id,
                groups.name AS group_name,
                images.path AS image_path,
                posts.text_content,
                posts.created_at,
                images.name AS image_name
            FROM posts
            JOIN users ON posts.user_id = users.id
            JOIN `groups` ON posts.group_id = groups.id
            LEFT JOIN images ON posts.image_id = images.id
            WHERE posts.group_id IN (?)
            ORDER BY posts.created_at DESC
            LIMIT 5
            ";
            // Get 5 Posts from the Group
            $posts = $db->getRows($sql, [$groupId]);

            sendSuccess(['posts' => $posts, 'user' => $userName]);
        } catch (Exception $e) {
            sendError('Failed create invite: ' . $e->getMessage(), 500);
        }
    }
}
