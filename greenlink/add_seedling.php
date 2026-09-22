<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

include 'db_config.php';
header('Content-Type: application/json');

// Get POST data (matching the Android App keys)
$seller_id   = isset($_POST['seller_id']) ? $_POST['seller_id'] : null;
$name        = isset($_POST['name']) ? $_POST['name'] : null; // This will go into 'crop_type'
$description = isset($_POST['description']) ? $_POST['description'] : null;
$type        = isset($_POST['type']) ? $_POST['type'] : null;
$soil_type   = isset($_POST['soil_type']) ? $_POST['soil_type'] : null;
$price       = isset($_POST['price']) ? $_POST['price'] : 0;

$image_url = ""; 

// Handle Image Upload
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $target_dir = "uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
    $file_name = "seedling_" . time() . "." . $file_extension;
    $target_file = $target_dir . $file_name;
    
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        $image_url = "http://10.0.2.2/greenlink/" . $target_file;
    }
}

// 2. Updated SQL to match your screenshot:
// user_id, crop_type, description, type, soil_type, price, image_url
$sql = "INSERT INTO seedlings (user_id, crop_type, description, type, soil_type, price, image_url) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    echo json_encode(["success" => false, "message" => "SQL Prepare Error: " . $conn->error]);
    exit;
}

// "issssds" -> int, string, string, string, string, double, string
$stmt->bind_param("issssds", $seller_id, $name, $description, $type, $soil_type, $price, $image_url);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Product added successfully!"]);
} else {
    echo json_encode(["success" => false, "message" => "Execution Error: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>