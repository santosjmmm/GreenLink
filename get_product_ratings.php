<?php
include 'db_config.php';
header('Content-Type: application/json');

$seedling_id = isset($_GET['seedling_id']) ? $_GET['seedling_id'] : null;
$seller_id = isset($_GET['seller_id']) ? $_GET['seller_id'] : null;

if ($seedling_id) {
    // Fetch ratings for a specific product
    $sql = "SELECT r.*, u.username FROM ratings r
            JOIN users u ON r.user_id = u.user_id
            JOIN order_items oi ON r.order_id = oi.order_id
            WHERE oi.seedling_id = '$seedling_id'
            ORDER BY r.created_at DESC";
} else if ($seller_id) {
    // Fetch all ratings for a seller's products (Consolidated)
    $sql = "SELECT r.*, u.username, s.crop_name 
            FROM ratings r
            JOIN users u ON r.user_id = u.user_id
            JOIN order_items oi ON r.order_id = oi.order_id
            JOIN seedlings s ON oi.seedling_id = s.seedling_id
            WHERE r.seller_id = '$seller_id'
            GROUP BY r.rating_id
            ORDER BY r.created_at DESC";
} else {
    echo json_encode([]); exit;
}

$result = $conn->query($sql);
$data = array();
while($row = $result->fetch_assoc()) { $data[] = $row; }
echo json_encode($data);
$conn->close();
?>