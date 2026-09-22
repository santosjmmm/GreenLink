<?php
include 'db_config.php';
$u = $_POST['user_id'];

$sql = "DELETE FROM cart WHERE user_id = '$u'";
if ($conn->query($sql) === TRUE) echo json_encode(["success" => true]);
$conn->close();
?>