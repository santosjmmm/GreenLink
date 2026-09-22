<?php
include 'db_config.php';
$u = $_POST['user_id'];
$s = $_POST['seedling_id'];

$sql = "DELETE FROM cart WHERE user_id = '$u' AND seedling_id = '$s'";
if ($conn->query($sql) === TRUE) echo json_encode(["success" => true]);
$conn->close();
?>