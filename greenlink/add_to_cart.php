<?php
include 'db_config.php';
$u = $_POST['user_id'];
$s = $_POST['seedling_id'];
$q = $_POST['quantity'];

$sql = "INSERT INTO cart (user_id, seedling_id, quantity) VALUES ('$u', '$s', '$q') 
        ON DUPLICATE KEY UPDATE quantity = quantity + $q";

if ($conn->query($sql) === TRUE) echo json_encode(["success" => true]);
else echo json_encode(["success" => false, "message" => $conn->error]);
$conn->close();
?>