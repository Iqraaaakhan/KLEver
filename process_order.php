<?php
session_start();

// --- THIS IS THE NEW, CRITICAL BLOCK TO READ JSON DATA ---
// Get the raw JSON data sent from the JavaScript fetch()
$json_data = file_get_contents('php://input');
// Decode the JSON string into a PHP object
$order_data = json_decode($json_data);

// Now, we check if the data was decoded correctly.
if (!$order_data) {
    // If the data is bad, we stop immediately.
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'Invalid order data received.']);
    exit;
}
// --- END OF NEW BLOCK ---

// Ensure the user is logged in (this is good for security)
if (!isset($_SESSION['user_id'])) {
    http_response_code(403); // Forbidden
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit;
}

// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'klever_db');
if ($conn->connect_error) { 
    http_response_code(500); // Server Error
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit; 
}

// Start a Database Transaction
$conn->begin_transaction();

try {
    // Get the data from the DECODED JSON object, not $_POST
    $name = $order_data->name;
    $email = $order_data->email;
    $payment_method = $order_data->payment; // Your JS sends it as 'payment'
    $total_price = $order_data->total;
    $order_items_from_js = $order_data->items; // This is the array of items

    $order_code = 'KLE-' . rand(1000, 9999);

    // Save the Main Order to the `orders` table
   // Get the logged-in user's ID from the session
$user_id = $_SESSION['user_id'];

// Save the Main Order to the `orders` table, now including the user_id
$stmt_order = $conn->prepare(
    "INSERT INTO orders (user_id, order_code, name, email, payment_method, total, status) VALUES (?, ?, ?, ?, ?, ?, 'Pending')"
);
// The bind_param string is updated from "ssssd" to "issssd" to include the new integer user_id
$stmt_order->bind_param("issssd", $user_id, $order_code, $name, $email, $payment_method, $total_price);
    $stmt_order->execute();

    // Get the ID of the New Order
    $new_order_id = $conn->insert_id;

    // Prepare and Save Each Item to the `order_items` table
    $stmt_items = $conn->prepare(
        "INSERT INTO order_items (order_id, item_id, item_name, quantity, price_per_item) VALUES (?, ?, ?, ?, ?)"
    );

    // The corrected block
foreach ($order_items_from_js as $item) {
    // We assume your JS item object has 'id', 'name', 'quantity', and 'cost' properties
    
    // THIS IS THE FIX: We split the string 'item_14' by the underscore '_'
    // and take the second part [1], which is the number. (int) ensures it's an integer.
    $item_id = (int)explode('_', $item->id)[1]; 

    $item_name = $item->name;
    $quantity = $item->quantity;
    $price = $item->cost / $item->quantity; // Calculate price per item
    
    $stmt_items->bind_param("isssd", $new_order_id, $item_id, $item_name, $quantity, $price);
    $stmt_items->execute();
}
    // If everything succeeded, commit the transaction
    $conn->commit();
    
    // Send a SUCCESS response back to the JavaScript
    echo json_encode(['success' => true, 'order_code' => $order_code]);

} catch (Exception $e) {
    // If any error occurred, roll back the transaction
    $conn->rollback();
    
    // Send a FAILURE response back to the JavaScript with the error message
    http_response_code(500); // Server Error
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>