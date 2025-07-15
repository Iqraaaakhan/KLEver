<?php
session_start();
header('Content-Type: application/json');

// Security Check: Ensure an admin is logged in.
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['new_order' => false]);
    exit;
}

// Get the ID of the most recent order the admin has already seen.
// The JavaScript will send this to us.
$last_known_id = isset($_GET['last_id']) ? (int)$_GET['last_id'] : 0;

// Connect to the database.
$conn = new mysqli('localhost', 'root', '', 'klever_db');
if ($conn->connect_error) { 
    echo json_encode(['new_order' => false]);
    exit; 
}

// This is the core query: Count how many orders exist with an ID GREATER than the last one seen.
$stmt = $conn->prepare("SELECT COUNT(*) as new_order_count FROM orders WHERE id > ?");
$stmt->bind_param("i", $last_known_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

// Check if there are any new orders.
if ($result['new_order_count'] > 0) {
    // If yes, send back a "true" signal.
    echo json_encode(['new_order' => true]);
} else {
    // If no, send back a "false" signal.
    echo json_encode(['new_order' => false]);
}

$stmt->close();
$conn->close();
?>