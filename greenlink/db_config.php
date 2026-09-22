<?php
// Railway dynamic variables
$host = getenv('MYSQLHOST');
$user = getenv('MYSQLUSER');
$pass = getenv('MYSQLPASSWORD');
$dbname = getenv('MYSQLDATABASE');
$port = getenv('MYSQLPORT');

// Connect using the variables
$conn = new mysqli($host, $user, $pass, $dbname, $port);

if ($conn->connect_error) {
    // This will show in Railway Logs if it fails
    error_log("Connection failed: " . $conn->connect_error);
    die(json_encode(["success" => false, "message" => "Database connection failed"]));
}
?>
