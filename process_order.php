<?php // process_order.php - ROBUST VERSION

// --- These two lines are for debugging ONLY. They force PHP to show all errors.
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// --- Use your one correct database ---
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'klever_db'; 

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database Connection Failed: " . $conn->connect_error]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (empty($data['name']) || empty($data['email']) || empty($data['payment']) || empty($data['items']) || !isset($data['total'])) {
    echo json_encode(["success" => false, "message" => "Invalid data sent from cart. Please fill all fields."]);
    exit;
}

// Sanitize the data
$name = $conn->real_escape_string($data['name']);
$email = $conn->real_escape_string($data['email']);
$payment = $conn->real_escape_string($data['payment']);
$order_details_json = json_encode($data['items']);
$total = floatval($data['total']);
$order_code = 'KLE-' . rand(1000, 9999);

// Prepare the SQL query
$sql = "INSERT INTO orders (order_code, name, email, payment_method, order_details, total)
        VALUES ('$order_code', '$name', '$email', '$payment', '$order_details_json', $total)";

// Execute the query AND check for errors
if ($conn->query($sql) === TRUE) {
    echo json_encode(["success" => true, "order_code" => $order_code]);
} else {
    // THIS IS THE CRITICAL PART: Tell the front-end EXACTLY what the database error is.
    echo json_encode(["success" => false, "message" => "Database Error: " . $conn->error]);
}

$conn->close();
?>