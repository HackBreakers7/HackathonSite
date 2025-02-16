<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Database connection using PDO
$host = "localhost";  // GoDaddy MySQL host is usually 'localhost'
$dbname = "hackathon"; // Your MySQL database name
$user = "hackathon"; // Your MySQL username
$pass = "Hackathon@2025"; // Your MySQL password

try {
    // Create a PDO connection with error handling
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Enable error exceptions
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // Fetch as associative array
    ]);

    // Query to fetch data
    $query = "SELECT * FROM teams";
    $stmt = $conn->prepare($query);
    $stmt->execute();

    // Fetch all results
    $teams = $stmt->fetchAll();

    echo json_encode(["success" => true, "teams" => $teams], JSON_PRETTY_PRINT);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
}
?>
