<?php
// --- START: PROFESSIONAL ERROR REPORTING & CHECKS ---
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check for critical PHP extensions
if (!function_exists('curl_init')) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Server Configuration Error: The cURL PHP extension is not enabled. Razorpay requires cURL.']);
    exit;
}
if (!function_exists('json_decode')) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Server Configuration Error: The JSON PHP extension is not enabled.']);
    exit;
}

// Check if the autoloader file exists
$autoloader_path = __DIR__ . '/vendor/autoload.php';
if (!file_exists($autoloader_path)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Server Configuration Error: Autoloader not found. Please run "composer install".']);
    exit;
}
require_once $autoloader_path;
// --- END: ERROR REPORTING & CHECKS ---

session_start();

use Razorpay\Api\Api;

header('Content-Type: application/json');

// Security Check: Ensure a user is logged in.
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit;
}

$json_data = file_get_contents('php://input');
$data = json_decode($json_data);
$order_total = $data->total ?? 0;

if ($order_total <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid order amount.']);
    exit;
}

// --- Your Razorpay API Keys ---
$KEY_ID = "rzp_test_grxxNZKvSUMJVN"; // THIS IS THE CORRECT KEY
$KEY_SECRET = "0lBSZD2rC0yx8KFmSUfajgKb"; // THIS IS THE CORRECT SECRET

try {
    $api = new Api($KEY_ID, $KEY_SECRET);
    $orderData = [
        'receipt'         => 'rcpt_' . time(),
        'amount'          => $order_total * 100, // Amount in paise
        'currency'        => 'INR'
    ];
    $razorpayOrder = $api->order->create($orderData);
    
    echo json_encode([
        'success' => true,
        'order_id' => $razorpayOrder['id'],
        'key' => $KEY_ID
    ]);

} catch (Exception $e) {
    // This will catch any errors from the Razorpay API itself
    echo json_encode(['success' => false, 'message' => 'Razorpay API Error: ' . $e->getMessage()]);
}
?>