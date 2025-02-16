<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Start output buffering to prevent unwanted output
ob_start(); 

require 'config.php'; 
require 'jwt_helper.php';

// Handle preflight request for CORS
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Ensure request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "error", "message" => "Invalid request method!"]);
    exit();
}

// Check if form data is set
if (!isset($_POST['name'], $_POST['email'], $_POST['phone'], $_POST['password'])) {
    echo json_encode(["status" => "error", "message" => "Invalid input received!"]);
    exit();
}

$name = $_POST['name'];
$email = $_POST['email'];
$mobile = $_POST['phone'];
$password = password_hash($_POST['password'], PASSWORD_BCRYPT);

// Database connection
try {
    $pdo = new PDO("mysql:host=localhost;dbname=hackathon;charset=utf8", "hackathon", "Hackathon@2025", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    // Check if email already exists
    $checkQuery = "SELECT id FROM users WHERE email = ?";
    $stmt = $pdo->prepare($checkQuery);
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(["status" => "error", "message" => "Email already exists"]);
        exit();
    }

    // Insert new user
    $query = "INSERT INTO users (name, email, mobile, password) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($query);
    $result = $stmt->execute([$name, $email, $mobile, $password]);

    if ($result) {
        $userId = $pdo->lastInsertId();

        // Generate JWT Token
        $token = generateJWT($userId, $email);

        // Clear unwanted output and send JSON response
        ob_end_clean(); 
        echo json_encode(["status" => "success", "token" => $token, "message" => "User registered successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Database insert error"]);
    }

} catch (PDOException $e) {
    ob_end_clean(); 
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>
