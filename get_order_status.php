<?php
session_start();
header('Content-Type: application/json');

// --- Security & Validation ---
// We must ensure a user is logged in and an order code is provided.
if (!isset($_SESSION['user_id']) || !isset($_GET['order_code'])) {
    // If not, send back an error status.
    echo json_encode(['status' => 'Error', 'message' => 'Not authorized or no order code provided.']);
    exit;
}

// --- Database Connection ---
$conn = new mysqli('localhost', 'root', '', 'klever_db');
if ($conn->connect_error) { 
    echo json_encode(['status' => 'Error', 'message' => 'Database connection failed.']);
    exit; 
}

// --- Fetch the Current Status ---
$order_code = $_GET['order_code'];

// We only fetch the 'status' column for maximum speed and efficiency.
$stmt = $conn->prepare("SELECT status FROM orders WHERE order_code = ?");
$stmt->bind_param("s", $order_code);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $order = $result->fetch_assoc();
    // Success! Send back the current status.
    echo json_encode(['status' => $order['status']]);
} else {
    // If no order is found with that code, send back an error.
    echo json_encode(['status' => 'Not Found']);
}

$stmt->close();
$conn->close();
?>