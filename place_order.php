<?php include 'db_config.php'; header('Content-Type: application/json');
$user_id = $_POST['user_id']; $total_amount = $_POST['total_amount']; $payment_method = $_POST['payment_method']; // 'COD' or 'Online' 
$bank_name = isset($_POST['bank_name']) ? $_POST['bank_name'] : null; $account_number = isset($_POST['account_number']) ? $_POST['account_number'] : null;
/* 1. Create the main order entry / / Note: You may need to add payment_method, bank_name, and account_number columns to your 'orders' table */ 
$sql_order = "INSERT INTO orders (user_id, total_amount, status, payment_method, bank_name, account_number) VALUES ('$user_id', '$total_amount', 'Pending', '$payment_method', '$bank_name', '$account_number')";
if ($conn->query($sql_order) === TRUE) { $order_id = $conn->insert_id;
/* 2. Move items from cart to order_items */
$sql_cart = "SELECT seedling_id, quantity FROM cart WHERE user_id = '$user_id'";
$cart_res = $conn->query($sql_cart);

while ($row = $cart_res->fetch_assoc()) {
    $sid = $row['seedling_id'];
    $qty = $row['quantity'];
    
    /* Get current price */
    $price_res = $conn->query("SELECT price, user_id FROM seedlings WHERE seedling_id = '$sid'");
    $seedling = $price_res->fetch_assoc();
    $price = $seedling['price'];
    $seller_id = $seedling['user_id'];

    /* Update seller_id in main order if not set (simplification: last item's seller) */
    $conn->query("UPDATE orders SET seller_id = '$seller_id' WHERE order_id = '$order_id'");

    $conn->query("INSERT INTO order_items (order_id, seedling_id, quantity, price_at_time) 
                  VALUES ('$order_id', '$sid', '$qty', '$price')");
}

/* 3. Clear the user's cart */
$conn->query("DELETE FROM cart WHERE user_id = '$user_id'");

echo json_encode(["success" => true]);
} else { echo json_encode(["success" => false, "message" => $conn->error]); }
$conn->close(); ?>