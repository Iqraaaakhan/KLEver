<?php
session_start();
header('Content-Type: application/json');

// Security Check: Only logged-in admins can access this data.
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['error' => 'Not authorized']);
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'klever_db');
if ($conn->connect_error) { 
    echo json_encode(['error' => 'Database connection failed']);
    exit; 
}

// This is the core logic: Fetch only active orders (Pending or Preparing).
// We also join with order_items to get all items for each order efficiently in one query.
$sql = "SELECT 
            o.id, 
            o.order_code, 
            o.status,
            o.order_time,
            oi.item_name, 
            oi.quantity 
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        WHERE o.status IN ('Pending', 'Preparing')
        ORDER BY o.order_time ASC"; // Oldest orders first

$result = $conn->query($sql);

$orders = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $order_id = $row['id'];
        // If we haven't seen this order ID yet, create a new entry for it.
        if (!isset($orders[$order_id])) {
            $orders[$order_id] = [
                'id' => $order_id,
                'order_code' => $row['order_code'],
                'status' => $row['status'],
                'order_time' => $row['order_time'],
                'items' => []
            ];
        }
        // Add the current item to this order's item list.
        $orders[$order_id]['items'][] = [
            'name' => $row['item_name'],
            'quantity' => $row['quantity']
        ];
    }
}

// We use array_values to convert the associative array into a simple indexed array for JSON.
echo json_encode(array_values($orders));

$conn->close();
?>