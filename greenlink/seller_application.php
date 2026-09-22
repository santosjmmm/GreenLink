<?php
include 'db_config.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Action: List pending applications for Admin
    $sql = "SELECT a.*, u.username, u.email, u.contact_num as phone 
            FROM seller_applications a 
            JOIN users u ON a.user_id = u.user_id 
            WHERE a.status = 'Pending'";
    $res = $conn->query($sql);
    $apps = array();
    while($r = $res->fetch_assoc()) { $apps[] = $r; }
    echo json_encode($apps);
} 
else if ($method === 'POST') {
    $action = $_POST['action'];

    if ($action === 'apply') {
        $user_id = $_POST['user_id'];
        $shop_name = $_POST['shop_name'];
        $shop_address = $_POST['shop_address'];

        function upload($file_key) {
            if(!isset($_FILES[$file_key])) return null;
            $target_dir = "uploads/";
            if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }
            $filename = time() . "_" . $file_key . ".jpg";
            $target_file = $target_dir . $filename;
            if(move_uploaded_file($_FILES[$file_key]["tmp_name"], $target_file)) {
                return "https://greenlink-production-4dcb.up.railway.app/" . $target_file;
            }
            return null;
        }

        $id_url = upload('id_image');
        $permit_url = upload('permit_image');
        $selfie_url = upload('selfie_image');

        $sql = "INSERT INTO seller_applications (user_id, shop_name, shop_address, id_image_url, permit_image_url, selfie_image_url) 
                VALUES ('$user_id', '$shop_name', '$shop_address', '$id_url', '$permit_url', '$selfie_url')";

        if ($conn->query($sql) === TRUE) {
            // 1. Notify user that application was received
            $notif_sql = "INSERT INTO notifications (user_id, title, message) 
                          VALUES ('$user_id', 'Application Received', 'Your request to become a seller for $shop_name is now being reviewed by the admin.')";
            $conn->query($notif_sql);
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => $conn->error]);
        }
    } 
    else if ($action === 'review') {
        $app_id = $_POST['application_id'];
        $user_id = $_POST['user_id'];
        $status = $_POST['status']; // 'Approved' or 'Declined'

        $sql = "UPDATE seller_applications SET status = '$status' WHERE application_id = '$app_id'";
        if ($conn->query($sql) === TRUE) {
            if ($status == 'Approved') {
                $conn->query("UPDATE users SET role = 'Seller' WHERE user_id = '$user_id'");
                $title = "Congratulations!";
                $msg = "Your application to be a seller has been APPROVED. You can now access your Seller Dashboard.";
            } else {
                $title = "Application Update";
                $msg = "Your application to be a seller was unfortunately declined. Please contact support for more details.";
            }
            
            // 2. Notify user of the final decision
            $notif_sql = "INSERT INTO notifications (user_id, title, message) VALUES ('$user_id', '$title', '$msg')";
            $conn->query($notif_sql);
            
            echo json_encode(["success" => true]);
        }
    }
}
$conn->close();
?>
