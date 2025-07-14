<?php // get_status.php
header('Content-Type: application/json');

if (!isset($_GET['order_code'])) {
    echo json_encode(['status' => null, 'error' => 'No order code provided.']);
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'klever_db');
if ($conn->connect_error) {
    echo json_encode(['status' => null, 'error' => 'Database connection failed.']);
    exit;
}

$order_code = $conn->real_escape_string($_GET['order_code']);

$stmt = $conn->prepare("SELECT status FROM orders WHERE order_code = ?");
$stmt->bind_param("s", $order_code);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $order = $result->fetch_assoc();
    echo json_encode(['status' => $order['status']]);
} else {
    echo json_encode(['status' => null, 'error' => 'Order not found.']);
}

$stmt->close();
$conn->close();
?>