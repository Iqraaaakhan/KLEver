<?php
session_start();
header('Content-Type: application/json');

// Security Check 1: Ensure a user is logged in.
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Authorization error.']);
    exit;
}

// Security Check 2: Ensure an order ID was sent.
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid order request.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$order_id = $_GET['id'];
$conn = new mysqli('localhost', 'root', '', 'klever_db');
if ($conn->connect_error) { 
    echo json_encode(['success' => false, 'message' => 'Database connection error.']);
    exit; 
}

// CRITICAL Security Check 3: Verify that the requested order actually belongs to the logged-in user.
// This prevents a user from reordering someone else's items by guessing order IDs.
$stmt_verify = $conn->prepare("SELECT id FROM orders WHERE id = ? AND user_id = ?");
$stmt_verify->bind_param("ii", $order_id, $user_id);
$stmt_verify->execute();
if ($stmt_verify->get_result()->num_rows == 0) {
    echo json_encode(['success' => false, 'message' => 'Order not found or does not belong to user.']);
    exit;
}

// If all security checks pass, fetch the items for that order.
$stmt_items = $conn->prepare("SELECT item_id, item_name, quantity, price_per_item FROM order_items WHERE order_id = ?");
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$result = $stmt_items->get_result();

$items_for_cart = [];
while ($row = $result->fetch_assoc()) {
    $items_for_cart[] = $row;
}

// Send the items back to the JavaScript as a JSON object.
echo json_encode(['success' => true, 'items' => $items_for_cart]);

$conn->close();
?>