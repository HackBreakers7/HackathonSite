<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require 'vendor/autoload.php'; // Load Composer dependencies

$secretKey = "mySuperKey123"; // Change this to a strong, secret key

function generateJWT($userId, $email) {
    global $secretKey;

    $payload = [
        "iss" => "https://hackathon.sknscoe.ac.in", // Issuer
        "aud" => "https://hackathon.sknscoe.ac.in", // Audience
        "iat" => time(),             // Issued at
        "exp" => time() + (60 * 60), // Expiry time (1 hour)
        "data" => [
            "id" => $userId,
            "email" => $email
        ]
    ];

    return JWT::encode($payload, $secretKey, 'HS256');
}

function verifyJWT($token) {
    global $secretKey;

    try {
        return JWT::decode($token, new Key($secretKey, 'HS256'));
    } catch (Exception $e) {
        return false;
    }
}
?>
