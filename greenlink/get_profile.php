<?php
include 'db_config.php';
header('Content-Type: application/json');

$user_id = $_GET['user_id'];

// We must explicitly SELECT the new columns
$sql = "SELECT user_id as id, username, email, contact_num as phone, full_name, address, region, province, city, barangay, latitude, longitude, role FROM users WHERE user_id = '$user_id'";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo json_encode($result->fetch_assoc());
} else {
    http_response_code(404);
    echo json_encode(["message" => "User not found"]);
}
$conn->close();
?>