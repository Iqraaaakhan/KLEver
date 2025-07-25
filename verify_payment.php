<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

$autoloader_path = __DIR__ . '/vendor/autoload.php';
if (!file_exists($autoloader_path)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Server Configuration Error: Autoloader not found.']);
    exit;
}
require_once $autoloader_path;

use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

header('Content-Type: application/json');

// --- Your Razorpay API Keys ---
$KEY_ID = "rzp_test_grxxNZKvSUMJVN"; // THIS IS THE CORRECT KEY
$KEY_SECRET = "0lBSZD2rC0yx8KFmSUfajgKb"; // THIS IS THE CORRECT SECRET
$success = false;

$json_data = file_get_contents('php://input');
$data = json_decode($json_data);

if (!$data || !isset($data->razorpay_payment_id)) {
    echo json_encode(['success' => false, 'message' => 'Invalid data received.']);
    exit;
}

try {
    $api = new Api($KEY_ID, $KEY_SECRET);
    $attributes = [
        'razorpay_order_id' => $data->razorpay_order_id,
        'razorpay_payment_id' => $data->razorpay_payment_id,
        'razorpay_signature' => $data->razorpay_signature
    ];
    $api->utility->verifyPaymentSignature($attributes);
    $success = true;
} catch (SignatureVerificationError $e) {
    $success = false;
    $message = 'Razorpay Error : ' . $e->getMessage();
}

if ($success === true) {
    $conn = new mysqli('localhost', 'root', '', 'klever_db');
    if ($conn->connect_error) { 
        echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
        exit; 
    }

    $conn->begin_transaction();
    try {
        $user_id = $_SESSION['user_id'];
        $name = $data->customer_name;
        $email = $data->customer_email;
        $total_price = $data->total;
        $order_items_from_js = $data->order_items;
        $order_code = 'KLE-' . rand(1000, 9999);

        $stmt_order = $conn->prepare("INSERT INTO orders (user_id, order_code, name, email, payment_method, total, status) VALUES (?, ?, ?, ?, 'Online', ?, 'Pending')");
        $stmt_order->bind_param("isssd", $user_id, $order_code, $name, $email, $total_price);
        $stmt_order->execute();
        $new_order_id = $conn->insert_id;

        $stmt_items = $conn->prepare("INSERT INTO order_items (order_id, item_id, item_name, quantity, price_per_item) VALUES (?, ?, ?, ?, ?)");
        foreach ($order_items_from_js as $item) {
            $item_id = (int)explode('_', $item->id)[1];
            $price = $item->cost / $item->quantity;
            $stmt_items->bind_param("isssd", $new_order_id, $item_id, $item->name, $item->quantity, $price);
            $stmt_items->execute();
        }
        
        $conn->commit();
        echo json_encode(['success' => true, 'order_code' => $order_code]);

    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Failed to save order: ' . $e->getMessage()]);
    }
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => $message]);
}
?>