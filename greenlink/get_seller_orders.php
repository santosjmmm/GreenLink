<?php
// Disable showing errors to the app, we will handle them in JSON
error_reporting(0);
ini_set('display_errors', 0);

include 'db_config.php';
header('Content-Type: application/json');

$orders = array();

// 1. Validate inputs
if (!isset($_GET['seller_id']) || !isset($_GET['status'])) {
    echo json_encode($orders); // Return empty list instead of crashing
    exit;
}

$seller_id = $_GET['seller_id'];
$status = $_GET['status'];

// 2. Build the query based on status
if ($status == "All") {
    $sql = "SELECT o.*, u.full_name as buyer_name 
            FROM orders o 
            LEFT JOIN users u ON o.user_id = u.user_id 
            WHERE o.seller_id = '$seller_id' 
            ORDER BY o.created_at DESC";
} else {
    $sql = "SELECT o.*, u.full_name as buyer_name 
            FROM orders o 
            LEFT JOIN users u ON o.user_id = u.user_id 
            WHERE o.seller_id = '$seller_id' AND o.status = '$status' 
            ORDER BY o.created_at DESC";
}

$res_orders = $conn->query($sql);

if ($res_orders && $res_orders->num_rows > 0) {
    while($order = $res_orders->fetch_assoc()) {
        $order_id = $order['order_id'];
        
        // 3. Fetch items for this specific order
        // Note: Using crop_name (ensure your seedlings table has this column name)
        $sql_items = "SELECT i.*, s.crop_name 
                      FROM order_items i 
                      LEFT JOIN seedlings s ON i.seedling_id = s.seedling_id 
                      WHERE i.order_id = '$order_id'";
        
        $res_items = $conn->query($sql_items);
        $items = array();
        if ($res_items) {
            while($item = $res_items->fetch_assoc()) {
                $items[] = $item;
            }
        }
        
        $order['items'] = $items;
        $orders[] = $order;
    }
}

// 4. Final Output
echo json_encode($orders);
$conn->close();
?>