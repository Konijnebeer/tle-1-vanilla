<?php

if (isset($_GET['action'])) {

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