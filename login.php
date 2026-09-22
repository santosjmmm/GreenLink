<?php
include 'db_config.php';
$identifier = $_POST['username']; 
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE (email = '$identifier' OR username = '$identifier') AND password = '$password'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo json_encode(["success" => true, "message" => "Login successful", "user" => $user]);
} else {
    echo json_encode(["success" => false, "message" => "Invalid credentials"]);
}
$conn->close();
?>