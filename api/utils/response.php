<?php

/**
 * JSON Response Utilities
 * Easy-to-use functions for sending JSON responses with proper HTTP status codes
 */

/**
 * Set common headers for JSON responses
 */
function setJsonHeaders()
{
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    header("Content-Type: application/json");
}

/**
 * Send a successful JSON response
 * @param mixed $data The data to send
 * @param int $statusCode HTTP status code (default: 200)
 */
function sendSuccess($data = [], $statusCode = 200)
{
    http_response_code($statusCode);
    setJsonHeaders();

    $response = [
        'success' => true,
        'data' => $data
    ];

    echo json_encode($response);
    exit;
}

/**
 * Send an error JSON response
 * @param string $message Error message
 * @param int $statusCode HTTP status code (default: 500)
 * @param mixed $details Additional error details (optional)
 */
function sendError($message, $statusCode = 500, $details = null)
{
    http_response_code($statusCode);
    setJsonHeaders();

    $response = [
        'success' => false,
        'error' => [
            'message' => $message,
            'code' => $statusCode
        ]
    ];

    if ($details !== null) {
        $response['error']['details'] = $details;
    }

    echo json_encode($response);
    exit;
}

/**
 * Send validation error response (400 Bad Request)
 * @param array $errors Array of validation errors
 */
function sendValidationError($errors)
{
    sendError('Validation failed', 400, $errors);
}

/**
 * Send unauthorized response (401 Unauthorized)
 * @param string $message Custom message (optional)
 */
function sendUnauthorized($message = 'Authentication required')
{
    sendError($message, 401);
}

/**
 * Send forbidden response (403 Forbidden)
 * @param string $message Custom message (optional)
 */
function sendForbidden($message = 'Access denied')
{
    sendError($message, 403);
}

/**
 * Send not found response (404 Not Found)
 * @param string $message Custom message (optional)
 */
function sendNotFound($message = 'Resource not found')
{
    sendError($message, 404);
}

/**
 * Send created response (201 Created)
 * @param mixed $data The created resource data
 */
function sendCreated($data = [])
{
    sendSuccess($data, 201);
}

/**
 * Send no content response (204 No Content)
 */
function sendNoContent()
{
    http_response_code(204);
    setJsonHeaders();
    exit;
}

// Legacy support - if $data variable exists, send it
// if (isset($data)) {
//     sendSuccess($data);
// }
