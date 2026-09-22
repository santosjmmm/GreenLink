<?php
include 'db_config.php';
header('Content-Type: application/json');

if (isset($_GET['seller_id'])) {
    $seller_id = $_GET['seller_id'];
    $filter = isset($_GET['filter']) ? $_GET['filter'] : null;

    if ($filter) {
        // --- SALES REPORT MODE ---
        $date_filter = "";
        if($filter == "week") $date_filter = "AND o.created_at >= DATE_SUB(NOW(), INTERVAL 1 WEEK)";
        else if($filter == "month") $date_filter = "AND o.created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
        else if($filter == "year") $date_filter = "AND o.created_at >= DATE_SUB(NOW(), INTERVAL 1 YEAR)";

        $sql = "SELECT s.seedling_id, s.crop_name, s.image_url, 
                       IFNULL(SUM(oi.quantity), 0) as total_qty_sold, 
                       IFNULL(SUM(oi.quantity * oi.price_at_time), 0) as total_earnings
                FROM order_items oi
                JOIN orders o ON oi.order_id = o.order_id
                JOIN seedlings s ON oi.seedling_id = s.seedling_id
                WHERE o.seller_id = '$seller_id' AND o.status = 'Completed' $date_filter
                GROUP BY s.seedling_id";

        $res = $conn->query($sql);
        $data = array();
        while($row = $res->fetch_assoc()) { $data[] = $row; }
        echo json_encode($data);

    } else {
        // --- DASHBOARD STATS MODE ---
        $sql_orders = "SELECT COUNT(*) as count FROM orders WHERE seller_id = '$seller_id' AND status != 'Declined'";
        $total_orders = $conn->query($sql_orders)->fetch_assoc()['count'];

        $sql_seedlings = "SELECT COUNT(*) as count FROM seedlings WHERE user_id = '$seller_id'";
        $total_seedlings = $conn->query($sql_seedlings)->fetch_assoc()['count'];

        $sql_rating = "SELECT IFNULL(AVG(rating), 0) as avg_rating FROM ratings WHERE seller_id = '$seller_id'";
        $average_rating = $conn->query($sql_rating)->fetch_assoc()['avg_rating'];

        $sql_sales = "SELECT IFNULL(SUM(total_amount - 50.00), 0) as total_sales FROM orders WHERE seller_id = '$seller_id' AND status = 'Completed'";
        $total_sales = $conn->query($sql_sales)->fetch_assoc()['total_sales'];

        echo json_encode([
            "total_orders" => (int)$total_orders,
            "total_seedlings" => (int)$total_seedlings,
            "average_rating" => round((float)$average_rating, 1),
            "total_sales" => (float)$total_sales
        ]);
    }
}
$conn->close();
?>