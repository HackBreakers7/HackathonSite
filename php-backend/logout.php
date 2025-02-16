<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

require 'jwt_helper.php';

// Handle preflight request for CORS
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Get Authorization Header
$headers = getallheaders();
$authHeader = $headers["Authorization"] ?? null;

if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    echo json_encode(["status" => "error", "message" => "Unauthorized. Token is missing."]);
    exit();
}

$jwtToken = $matches[1]; // Extract token

// Verify the token
$decoded = verifyJWT($jwtToken);

if (!$decoded) {
    echo json_encode(["status" => "error", "message" => "Invalid or expired token."]);
    exit();
}

// Invalidate token by generating a short-lived token
$invalidToken = generateJWT($decoded->data->id, $decoded->data->email, time() + 1); // 1-second expiry

echo json_encode(["status" => "success", "message" => "Logged out successfully", "invalidToken" => $invalidToken]);
exit();
?>
