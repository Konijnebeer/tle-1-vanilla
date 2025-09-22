# here is some bioler plate for backend and front end comunication

```php yourname.php
<?php

require_once 'includes/database.php';

if (isset($_GET['action'])) {
    $action = $_GET['action'];
    
    if ($action === 'getUsers') {
        try {
            $db = Database::getInstance();
            $users = $db->fetchAll("SELECT * FROM users");
            $data = ['users' => $users];
        } catch (Exception $e) {
            $data = ['error' => 'Failed to fetch users'];
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
```

```js yourname.js
const url = "./api/yourname.php";

function success(data) {
  console.log(data);
}
ajaxRequest(url + "?action=getUsers", success);

function ajaxRequest(url, successCallback) {
  fetch(url)
    .then((response) => {
      if (!response.ok) {
        throw new Error(response.statusText);
      }
      return response.json();
    })
    .then((data) => successCallback(data))
    .catch((error) => ajaxRequestErrorHandler(error));
}

function ajaxRequestErrorHandler(error) {
  console.error("Error:", error);
  alert("An error occurred while fetching data. Please try again later.");
}
```
