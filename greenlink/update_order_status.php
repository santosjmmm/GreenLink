<?php include 'db_config.php'; header('Content-Type: application/json');
$order_id = $_POST['order_id']; $status = $_POST['status']; // 'Approved', 'Shipped', or 'Completed'
$sql = "UPDATE orders SET status = '$status' WHERE order_id = '$order_id'";
if ($conn->query($sql) === TRUE) { // 1. Get buyer ID to send them a notification
$sql_info = "SELECT user_id FROM orders WHERE order_id = '$order_id'"; $buyer_id = $conn->query($sql_info)->fetch_assoc()['user_id'];
// 2. Dynamic Notification Messages
$notif_title = "Order Status Update";
if($status == 'Approved') $notif_msg = "Great news! Your order #$order_id has been Approved and is preparing for shipment.";
else if($status == 'Shipped') $notif_msg = "Your order #$order_id is now on its way to you!";
else if($status == 'Completed') $notif_msg = "Order #$order_id is completed. Thank you for shopping!";
else $notif_msg = "Your order #$order_id status changed to $status.";

$conn->query("INSERT INTO notifications (user_id, title, message) VALUES ('$buyer_id', '$notif_title', '$notif_msg')");

echo json_encode(["success" => true, "message" => "Status updated to $status"]);
} else { echo json_encode(["success" => false, "message" => $conn->error]); } $conn->close(); ?>