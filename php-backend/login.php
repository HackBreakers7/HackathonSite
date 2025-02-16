<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

ob_start();

require 'config.php'; 
require 'jwt_helper.php';

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
$sql = "SELECT * FROM users WHERE email = :email";
$stmt = $conn->prepare($sql);
$stmt->bindValue(":email", $email, PDO::PARAM_STR);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch single user

        if (!$stmt) {
            die("Statement preparation failed: " . $conn->error);
        }
        
        if (!$stmt->execute()) {
            die("Query execution failed: " . $stmt->error);
        }        

        

if ($user) { 
    if (password_verify($password, $user['password'])) {  // Compare hashed password
        $token = generateJWT($user['id'], $email);
        
        ob_end_clean();
        echo json_encode([
            "status" => "success",
            "token" => $token,
            "is_admin" => $user['is_admin']
        ]);
        exit();
    } else {
        ob_end_clean();
        echo json_encode(["status" => "error", "message" => "Invalid email or password inner condition"]);
        exit();
    }
    } else {
        ob_end_clean();
        echo json_encode(["status" => "error", "message" => "Invalid email or password outer condition"]);
        exit();
        }
    } catch (Exception $e) {
        ob_end_clean();
        echo json_encode(["status" => "error", "message" => "Server error: " . $e->getMessage()]);
        exit();
    }
} else {
    ob_end_clean();
    echo json_encode(["status" => "error", "message" => "Missing email or password"]);
    exit();
}
?>
