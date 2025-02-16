<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

$host = "localhost";
$db_name = "hackathon";
$username = "hackathon";
$password = "Hackathon@2025";

// Database connection using PDO
try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database connection failed: " . $e->getMessage()]);
    exit;
}

// Include JWT functions
require './jwt_helper.php';

// Get headers and check Authorization
$headers = getallheaders();
$authHeader = $headers["Authorization"] ?? null;

if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    echo json_encode(["success" => false, "message" => "Unauthorized. Please log in first."]);
    exit;
}

$jwtToken = $matches[1];
$decoded = verifyJWT($jwtToken);

if (!$decoded) {
    echo json_encode(["success" => false, "message" => "Invalid or expired token."]);
    exit;
}

// Extract user details from token
$userId = $decoded->data->id ?? null;
$userEmail = $decoded->data->email ?? null;

if (!$userId) {
    echo json_encode(["success" => false, "message" => "Invalid token."]);
    exit;
}

// Fetch POST data safely
$teamName = $_POST["teamName"] ?? null;
$leaderName = $_POST["leaderName"] ?? null;
$collegeName = $_POST["collegeName"] ?? null;
$collegeAddress = $_POST["collegeAddress"] ?? null;
$state = $_POST["state"] ?? null;
$district = $_POST["district"] ?? null;
$taluka = $_POST["taluka"] ?? null;
$track = $_POST["track"] ?? null;
$numTeammates = $_POST["numTeammates"] ?? null;
$leaderEmail = $_POST["leaderEmail"] ?? null;
$contactNo = $_POST["contactNo"] ?? null;
$transactionId = $_POST["transactionId"] ?? null;
$transactionDate = $_POST["transactionDate"] ?? null;

// Validate required fields
$requiredFields = compact(
    "teamName", "leaderName", "collegeName", "collegeAddress",
    "state", "district", "taluka", "track", "numTeammates",
    "leaderEmail", "contactNo", "transactionId", "transactionDate"
);

foreach ($requiredFields as $field => $value) {
    if (empty($value)) {
        echo json_encode(["success" => false, "message" => "$field is required"]);
        exit;
    }
}

// Handle transaction photo upload
if (!isset($_FILES["transactionPhoto"]) || $_FILES["transactionPhoto"]["error"] != UPLOAD_ERR_OK) {
    echo json_encode(["success" => false, "message" => "Transaction Photo is required"]);
    exit;
}

$target_dir = "uploads/";
if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

$transaction_file = $target_dir . uniqid("txn_") . "_" . basename($_FILES["transactionPhoto"]["name"]);
move_uploaded_file($_FILES["transactionPhoto"]["tmp_name"], $transaction_file);

// Optional idea document
$ideaDocumentFile = null;
if (isset($_FILES["ideaDocument"]) && $_FILES["ideaDocument"]["error"] == UPLOAD_ERR_OK) {
    $ideaDocumentFile = $target_dir . uniqid("idea_") . "_" . basename($_FILES["ideaDocument"]["name"]);
    move_uploaded_file($_FILES["ideaDocument"]["tmp_name"], $ideaDocumentFile);
}

// Insert into MySQL teams table using PDO prepared statements
$query = "INSERT INTO teams 
    (team_name, leader_name, leader_email, contact_no, college_name, college_address, state, district, taluka, track, transaction_id, transaction_date, transaction_photo, idea_document, num_teammates)
    VALUES (:teamName, :leaderName, :leaderEmail, :contactNo, :collegeName, :collegeAddress, :state, :district, :taluka, :track, :transactionId, :transactionDate, :transactionPhoto, :ideaDocument, :numTeammates)";

$stmt = $conn->prepare($query);
$result = $stmt->execute([
    ":teamName" => $teamName,
    ":leaderName" => $leaderName,
    ":leaderEmail" => $leaderEmail,
    ":contactNo" => $contactNo,
    ":collegeName" => $collegeName,
    ":collegeAddress" => $collegeAddress,
    ":state" => $state,
    ":district" => $district,
    ":taluka" => $taluka,
    ":track" => $track,
    ":transactionId" => $transactionId,
    ":transactionDate" => $transactionDate,
    ":transactionPhoto" => $transaction_file,
    ":ideaDocument" => $ideaDocumentFile,
    ":numTeammates" => $numTeammates
]);

if ($result) {
    echo json_encode(["success" => true, "message" => "Team registered successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Database insert failed"]);
}
?>
