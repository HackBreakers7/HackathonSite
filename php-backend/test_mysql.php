<?php
$host = "localhost";  
$db_name = "hackathon";
$username = "hackathon";
$password = "Hackathon@2025";

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $username, $password);
    echo "MySQL Connected Successfully!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
