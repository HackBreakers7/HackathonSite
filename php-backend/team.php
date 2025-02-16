<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Database connection using PDO
$host = "localhost";  // Your MySQL host
$dbname = "hackathon"; // Your database name
$user = "hackathon"; // Your database username
$pass = "Hackathon@2025"; // Your database password

try {
    // Create a PDO instance
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    // Check if team ID is provided
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        echo json_encode(["success" => false, "message" => "Team ID is required"]);
        exit;
    }

    $teamId = $_GET['id'];

    // Query to fetch team details
    $query = "SELECT * FROM teams WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $teamId, PDO::PARAM_INT);
    $stmt->execute();
    $team = $stmt->fetch();

    // If no team found, return error message
    if (!$team) {
        echo json_encode(["success" => false, "message" => "Team not found"]);
        exit;
    }

    // Return the team details in JSON format
    echo json_encode($team, JSON_PRETTY_PRINT);

} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
}
?>
