<?php
include 'db_config.php';
header('Content-Type: application/json');

/* 
   1. READ PARAMETERS FROM ANDROID APP
   We clean the inputs to ensure resets like "All Types" don't block results.
*/
$seller_id = (isset($_GET['seller_id']) && !empty($_GET['seller_id'])) ? $_GET['seller_id'] : null;

$type = isset($_GET['type']) ? $_GET['type'] : null;
if ($type == 'All' || $type == 'All Types' || empty($type) || $type == 'null') { $type = null; }

$soil = isset($_GET['soil_type']) ? $_GET['soil_type'] : null;
if ($soil == 'All' || $soil == 'All Soils' || empty($soil) || $soil == 'null') { $soil = null; }

$search = isset($_GET['search']) && !empty($_GET['search']) ? $_GET['search'] : null;

$top_selling = (isset($_GET['top_selling']) && $_GET['top_selling'] == 'true');

/* 
   2. BASE SQL STRUCTURE
   We use LEFT JOIN so seedlings show up even if seller data is missing.
*/
$sql = "SELECT s.*, u.full_name as seller_name FROM seedlings s 
        LEFT JOIN users u ON s.user_id = u.user_id 
        WHERE 1=1";

/* 
   3. APPLY FILTERS & SEARCH
*/
if ($seller_id) { 
    $sql .= " AND s.user_id = '$seller_id'"; 
}

if ($type) { 
    $sql .= " AND s.type LIKE '$type'"; 
}

if ($soil) { 
    $sql .= " AND s.soil_type LIKE '$soil'"; 
}

if ($search) {
    /* Search matches either the product name OR the description */
    $sql .= " AND (s.crop_name LIKE '%$search%' OR s.description LIKE '%$search%')";
}

/* 
   4. HANDLE TOP SELLING MODE
*/
if ($top_selling) {
    /* Override query to calculate sales volume from completed orders */
    $sql = "SELECT s.*, u.full_name as seller_name, IFNULL(SUM(oi.quantity), 0) as sales_count 
            FROM seedlings s 
            LEFT JOIN users u ON s.user_id = u.user_id 
            LEFT JOIN order_items oi ON s.seedling_id = oi.seedling_id
            LEFT JOIN orders o ON oi.order_id = o.order_id AND o.status = 'Completed'
            WHERE 1=1";
            
    if ($seller_id) $sql .= " AND s.user_id = '$seller_id'";
    if ($type) $sql .= " AND s.type LIKE '$type'";
    if ($soil) $sql .= " AND s.soil_type LIKE '$soil'";
    if ($search) $sql .= " AND (s.crop_name LIKE '%$search%' OR s.description LIKE '%$search%')";
    
    $sql .= " GROUP BY s.seedling_id ORDER BY sales_count DESC LIMIT 10";
} else {
    /* Default: Show newest items first */
    $sql .= " ORDER BY s.seedling_id DESC";
}

/* 
   5. EXECUTE AND FORMAT JSON
*/
$result = $conn->query($sql);
$seedlings = array();

if ($result) {
    while($row = $result->fetch_assoc()) {
        /* MAP DATABASE COLUMNS TO APP MODEL */
        $seedlings[] = array(
            "id" => (int)$row['seedling_id'],
            "seller_id" => (int)$row['user_id'],
            "seller_name" => $row['seller_name'] ?: "Unknown Seller",
            "name" => $row['crop_name'],
            "description" => $row['description'],
            "type" => $row['type'],
            "soil_type" => $row['soil_type'],
            "price" => (double)$row['price'],
            "image_url" => $row['image_url']
        );
    }
}

echo json_encode($seedlings);

$conn->close();
?>