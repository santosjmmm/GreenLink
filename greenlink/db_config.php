<?php
// Railway Environment Variables
$host = getenv('MYSQLHOST') ?: getenv('MYSQL_HOST');
$user = getenv('MYSQLUSER') ?: getenv('MYSQL_USER');
$pass = getenv('MYSQLPASSWORD') ?: getenv('MYSQL_PASSWORD');
$dbname = getenv('MYSQLDATABASE') ?: getenv('MYSQL_DATABASE');
$port = getenv('MYSQLPORT') ?: getenv('MYSQL_PORT') ?: "3306";

// Error reporting for debugging (only while setting up)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($host, $user, $pass, $dbname, $port);
    $conn->set_charset("utf8mb4");
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}
?>
