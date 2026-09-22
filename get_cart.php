<?php
include 'db_config.php';
$user_id = $_GET['user_id'];

$sql = "SELECT s.seedling_id as id, s.crop_name as name, s.price, s.image_url, c.quantity 
        FROM cart c 
        JOIN seedlings s ON c.seedling_id = s.seedling_id 
        WHERE c.user_id = '$user_id'";

$result = $conn->query($sql);
$rows = array();
while($r = $result->fetch_assoc()) { $rows[] = $r; }
echo json_encode($rows);
$conn->close();
?>