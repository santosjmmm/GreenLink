<?php
include 'db_config.php';
header('Content-Type: application/json');

$user_id = $_GET['user_id'];
$status = $_GET['status']; // 'Pending', 'Approved', 'Shipped', 'Completed', 'ToRate'

if ($status == 'ToRate') {
    // Completed but NOT yet rated
    $sql = "SELECT o.*, (SELECT COUNT(*) FROM ratings r WHERE r.order_id = o.order_id) as is_rated
            FROM orders o WHERE o.user_id = '$user_id' AND o.status = 'Completed' 
            HAVING is_rated = 0 ORDER BY o.created_at DESC";
} else {
    $sql = "SELECT o.*, (SELECT COUNT(*) FROM ratings r WHERE r.order_id = o.order_id) as is_rated
            FROM orders o WHERE o.user_id = '$user_id' AND o.status = '$status' 
            ORDER BY o.created_at DESC";
}

$res = $conn->query($sql);
$orders = array();
while ($row = $res->fetch_assoc()) {
    $order_id = $row['order_id'];
    $item_sql = "SELECT oi.*, s.crop_name, s.image_url FROM order_items oi 
                 JOIN seedlings s ON oi.seedling_id = s.seedling_id WHERE oi.order_id = '$order_id'";
    $item_res = $conn->query($item_sql);
    $items = array();
    while ($item_row = $item_res->fetch_assoc()) { $items[] = $item_row; }
    $row['items'] = $items;
    $orders[] = $row;
}
echo json_encode($orders);
$conn->close();
?>