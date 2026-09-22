<?php
include 'db_config.php';
header('Content-Type: application/json');

$user_id = $_GET['user_id'];

// ADD THIS LINE: Marks all notifications as read as soon as the list is loaded
$conn->query("UPDATE notifications SET is_read = 1 WHERE user_id = '$user_id'");

$sql = "SELECT * FROM notifications WHERE user_id = '$user_id' ORDER BY created_at DESC";
$result = $conn->query($sql);

$notifs = array();
while($row = $result->fetch_assoc()) {
    $notifs[] = $row;
}

echo json_encode($notifs);
$conn->close();
?>