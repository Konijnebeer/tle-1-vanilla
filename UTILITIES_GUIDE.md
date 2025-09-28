# Utility Functions Usage Guide

## 1. Response.php - Easy JSON Responses

### Basic Usage:

```php
require_once 'utils/response.php';

// Success response
sendSuccess(['message' => 'Data retrieved successfully', 'data' => $results]);

// Error response
sendError('Something went wrong', 500);

// Validation errors
$errors = ['email' => 'Email is required', 'password' => 'Password too short'];
sendValidationError($errors);

// Common HTTP responses
sendUnauthorized(); // 401
sendForbidden();    // 403
sendNotFound();     // 404
sendCreated($newRecord); // 201
```

### Response Format:

Success responses look like:

```json
{
  "success": true,
  "data": { "your": "data" }
}
```

Error responses look like:

```json
{
  "success": false,
  "error": {
    "message": "Error description",
    "code": 400,
    "details": { "optional": "details" }
  }
}
```

## 2. Account.php - Server-side Authentication

### Basic Usage:

```php
require_once 'utils/acount.php';

// Check if user is logged in
if (isUserLoggedIn()) {
    echo "Welcome back!";
}

// Get current user data
$user = getCurrentUser();
if ($user) {
    echo "Hello " . $user['username'];
}

// Require authentication (sends 401 if not logged in)
requireAuth();

// Get user ID
$userId = getCurrentUserId();

// Check user groups
if (userHasGroup(1)) {
    echo "You are an admin!";
}
```

## 3. Fetch.js - Easy AJAX Requests

### Basic Usage:

```javascript
import { ajaxRequestGET, ajaxRequestPOST } from "./utils/fetch.js";

// GET request
ajaxRequestGET("../api/posts.php", (data) => {
  console.log("Posts:", data);
});

// POST request with JSON data
ajaxRequestPOST(
  "../api/createpost.php",
  (data) => {
    console.log("Post created:", data);
  },
  {
    title: "My Post",
    content: "Post content",
  }
);

// POST request with FormData (file uploads)
const formData = new FormData();
formData.append("image", fileInput.files[0]);
ajaxRequestPOST(
  "../api/upload.php",
  (data) => {
    console.log("File uploaded:", data);
  },
  formData
);

// Custom error handling
ajaxRequestGET(
  "../api/data.php",
  (data) => console.log("Success:", data),
  (error) => console.log("Custom error handling:", error)
);
```

## 4. Account.js - Client-side Authentication

### Basic Usage:

```javascript
import {
  requireLogin,
  checkAuthStatus,
  getCurrentUser,
  logout,
} from "./utils/acount.js";

// Require login on protected pages
requireLogin(); // Redirects to ../start.html if not logged in

// Check auth without redirecting
checkAuthStatus(
  (userData) => console.log("Logged in:", userData.user.username),
  () => console.log("Not logged in")
);

// Get current user data
getCurrentUser((user) => {
  if (user) {
    document.getElementById("username").textContent = user.username;
  }
});

// Logout
document.getElementById("logoutBtn").onclick = () => logout();

// Auto-initialize auth check
import { initAuth } from "./utils/acount.js";
initAuth(true); // true = require auth, false = just show/hide elements
```

## 5. Complete Example: Protected API Endpoint

### PHP (api/protected-endpoint.php):

```php
<?php
require_once 'utils/response.php';
require_once 'utils/acount.php';

// Require authentication
requireAuth();

// Get current user
$user = getCurrentUser();

// Validate input
if (empty($_POST['title'])) {
    sendValidationError(['title' => 'Title is required']);
}

// Process request
try {
    $result = createPost($_POST['title'], $_POST['content'], $user['id']);
    sendCreated($result);
} catch (Exception $e) {
    sendError('Failed to create post', 500);
}
?>
```

### JavaScript:

```javascript
import { ajaxRequestPOST } from "./utils/fetch.js";
import { requireLogin } from "./utils/acount.js";

// Ensure user is logged in
requireLogin();

// Make authenticated request
function createPost(title, content) {
  ajaxRequestPOST(
    "../api/protected-endpoint.php",
    (data) => {
      alert("Post created successfully!");
      window.location.href = "../home.html";
    },
    {
      title: title,
      content: content,
    }
  );
}
```

## 6. Migration from Old Code

### Old way:

```php
// OLD
header('Content-Type: application/json');
echo json_encode(['status' => 'success', 'data' => $data]);
```

### New way:

```php
// NEW
sendSuccess($data);
```

### Old way:

```javascript
// OLD
fetch(url)
  .then((response) => response.json())
  .then((data) => callback(data));
```

### New way:

```javascript
// NEW
ajaxRequestGET(url, callback);
```
