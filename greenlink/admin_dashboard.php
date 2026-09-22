<?php include 'db_config.php'; header('Content-Type: application/json');
$action = isset($_GET['action']) ? $_GET['action'] : 'get_stats';
if ($action === 'get_stats') { // 1. Get Pending Application Count 
$sql_queue = "SELECT COUNT(*) as count FROM seller_applications WHERE status = 'Pending'"; $res_queue = $conn->query($sql_queue); $queue_count = $res_queue->fetch_assoc()['count'];
// 2. Get Total Approved Seller Count
$sql_sellers_count = "SELECT COUNT(*) as count FROM users WHERE role = 'Seller'";
$res_sellers_count = $conn->query($sql_sellers_count);
$seller_count = $res_sellers_count->fetch_assoc()['count'];

echo json_encode([
    "approval_queue_count" => (int)$queue_count,
    "approved_seller_count" => (int)$seller_count
]);
} else if ($action === 'get_sellers') { // 3. Get all verified Sellers - ADDED ADDRESS COLUMNS HERE 
$sql = "SELECT user_id as id, username, email, contact_num as phone, full_name, role, address, region, province, city, barangay FROM users WHERE role = 'Seller'";
$result = $conn->query($sql);
$sellers = array();
while($row = $result->fetch_assoc()) {
    $sellers[] = $row;
}
echo json_encode($sellers);
} $conn->close(); ?>