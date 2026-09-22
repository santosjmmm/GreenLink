<?php
include 'db_config.php';
header('Content-Type: application/json');

$order_id = $_POST['order_id'];
$user_id = $_POST['user_id'];
$seller_id = $_POST['seller_id'];
$rating = $_POST['rating'];
$comment = $_POST['comment'];

$sql = "INSERT INTO ratings (order_id, user_id, seller_id, rating, comment) 
        VALUES ('$order_id', '$user_id', '$seller_id', '$rating', '$comment')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["success" => true, "message" => "Rating submitted!"]);
} else {
    echo json_encode(["success" => false, "message" => "Error: " . $conn->error]);
}
$conn->close();
?>