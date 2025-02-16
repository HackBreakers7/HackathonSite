<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");


$host = "localhost";  // GoDaddy MySQL host is usually 'localhost'
$dbname = "hackathon"; // Your MySQL database name
$user = "hackathon"; // Your MySQL username
$pass = "Hackathon@2025"; // Your MySQL password

try {

    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Enable error exceptions
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // Fetch as associative array
    ]);

    // Check if request is POST
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        echo json_encode(["success" => false, "message" => "Invalid request method"]);
        exit;
    }

    if (empty($_POST)) {
        echo json_encode(["success" => false, "message" => "No POST data received. Check FormData format"]);
        exit;
    }
    
    if (!isset($_POST["id"]) || !isset($_POST["rating"])) {
        echo json_encode(["success" => false, "message" => "Missing required fields"]);
        exit;
    }

    try{
   $id = intval($_POST["id"]);
    $rating = floatval($_POST["rating"]);

    // Debugging: Check values received
    error_log("Received team_id: $id, rating: $rating");

    // Check if team exists before updating
    $checkQuery = "SELECT id FROM teams WHERE id = :id";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bindValue(":id", $id, PDO::PARAM_INT);
    $checkStmt->execute();

    if ($checkStmt->rowCount() === 0) {
        echo json_encode(["success" => false, "message" => "Team not found"]);
        exit;
    }

    // Update query using prepared statement
    $query = "UPDATE teams SET rating = :rating WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindValue(":rating", $rating);
    $stmt->bindValue(":id", $id, PDO::PARAM_INT);
    $stmt->execute();

    if (!$stmt) {
        die("Statement preparation failed: " . $conn->error);
    }
    
    if (!$stmt->execute()) {
        die("Query execution failed: " . $stmt->error);
    }   
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Rating updated successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to update rating inner"]);
    }
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
    }

 
}    catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
}
?>
