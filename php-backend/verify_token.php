<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require 'jwt_helper.php'; // Ensure this file correctly handles JWT creation & verification

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Fetch Authorization headers
$headers = getallheaders();
$authHeader = $headers["Authorization"] ?? null;

if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    echo json_encode(["status" => "error", "message" => "Token not provided"]);
    exit();
}

$token = $matches[1]; // Extract token

try {
    // Verify JWT
    $decoded = verifyJWT($token);
    
    if ($decoded) {
        echo json_encode([
            "status" => "success",
            "message" => "Token is valid",
            "user" => $decoded
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid token"]);
    }
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Token verification failed: " . $e->getMessage()]);
}
?>
