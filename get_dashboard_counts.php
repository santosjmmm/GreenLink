<?php include 'db_config.php'; header('Content-Type: application/json');
$user_id = $_GET['user_id'];
// 1. Count unread notifications 
$sql_notifs = "SELECT COUNT(*) as count FROM notifications WHERE user_id = '$user_id' AND is_read = 0"; $res_notifs = $conn->query($sql_notifs); $unread_notifs = $res_notifs->fetch_assoc()['count'];
// 2. Count items in cart 
$sql_cart = "SELECT SUM(quantity) as count FROM cart WHERE user_id = '$user_id'"; $res_cart = $conn->query($sql_cart); $cart_items = $res_cart->fetch_assoc()['count'];
// 3. Count unread chat messages 
$sql_chats = "SELECT COUNT(*) as count FROM messages WHERE receiver_id = '$user_id' AND is_read = 0"; $res_chats = $conn->query($sql_chats); $unread_chats = $res_chats->fetch_assoc()['count'];
echo json_encode([ "unread_notifs" => (int)$unread_notifs, "cart_items" => (int)$cart_items, "unread_chats" => (int)$unread_chats ]);
$conn->close(); ?>