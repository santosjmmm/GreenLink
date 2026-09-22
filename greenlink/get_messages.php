<?php
include 'db_config.php';
header('Content-Type: application/json');

$u1 = $_GET['sender_id']; // current user
$u2 = $_GET['receiver_id']; // other person

// Marks messages from the other person to you as read
$conn->query("UPDATE messages SET is_read = 1 WHERE sender_id = '$u2' AND receiver_id = '$u1'");

$sql = "SELECT m.*, u.username as sender_name FROM messages m
        JOIN users u ON m.sender_id = u.user_id
        WHERE (sender_id = '$u1' AND receiver_id = '$u2') 
           OR (sender_id = '$u2' AND receiver_id = '$u1') 
        ORDER BY created_at ASC";

$result = $conn->query($sql);
$rows = array();
while($r = $result->fetch_assoc()) { $rows[] = $r; }
echo json_encode($rows);
$conn->close();
?>