<?php
require_once 'includes/database.php';
if (isset($_GET['action'])) {
    $action = $_GET['action'];

    if ($action === 'getall') {
        try {
            $db = Database::getInstance();
            $groupIds = [1, 2, 3, 4,5]; // Example, can be any length

            $placeholders = implode(',', array_fill(0, count($groupIds), '?'));
            $sql = "
SELECT 
    posts.id,
    users.username,
    posts.group_id,
    images.path AS image_path,
    posts.text_content,
    posts.created_at,
    images.name
FROM posts
JOIN users ON posts.user_id = users.id
LEFT JOIN images ON posts.image_id = images.id
WHERE posts.group_id IN ($placeholders)
";

            $posts = $db->fetchAll($sql, $groupIds);
            //    CONCAT('/images/', images.name, '.', images.extension) AS image_path,
            $data = ['posts' => $posts];
        } catch (Exception $e) {
            $data = ['error' => 'Failed to fetch posts'];
        }
    } else {
        $data = ['error' => 'Unknown action'];
    }
} else {
    $data = ['error' => 'Action not specified'];
}

// Set the header & output JSON so the client will know what to expect.
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");
echo json_encode($data);
exit;
?>
