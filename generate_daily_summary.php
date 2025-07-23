<?php
// This script is designed to be run automatically once per day (e.g., at 11:59 PM).

// --- DATABASE CONNECTION ---
$conn = new mysqli('localhost', 'root', '', 'klever_db');
if ($conn->connect_error) { 
    die("Connection Failed: " . $conn->connect_error); 
}

echo "Connected to database successfully.<br>";

// --- 1. CALCULATE METRICS FOR TODAY ---

// Get today's date in 'YYYY-MM-DD' format.
$today_date = date('Y-m-d');

// Metric 1: Total Sales for Today
$stmt_sales = $conn->prepare("SELECT SUM(total) as total_sales FROM orders WHERE DATE(order_time) = ?");
$stmt_sales->bind_param("s", $today_date);
$stmt_sales->execute();
$sales_today = $stmt_sales->get_result()->fetch_assoc()['total_sales'] ?? 0;

// Metric 2: Number of Orders Today
$stmt_orders = $conn->prepare("SELECT COUNT(id) as order_count FROM orders WHERE DATE(order_time) = ?");
$stmt_orders->bind_param("s", $today_date);
$stmt_orders->execute();
$orders_today = $stmt_orders->get_result()->fetch_assoc()['order_count'] ?? 0;

// Metric 3: Top Selling Item of the Day
$stmt_top_item = $conn->prepare(
    "SELECT oi.item_name, SUM(oi.quantity) as total_quantity 
     FROM order_items oi
     JOIN orders o ON oi.order_id = o.id
     WHERE DATE(o.order_time) = ?
     GROUP BY oi.item_name 
     ORDER BY total_quantity DESC 
     LIMIT 1"
);
$stmt_top_item->bind_param("s", $today_date);
$stmt_top_item->execute();
$top_item_result = $stmt_top_item->get_result()->fetch_assoc();
$top_item_name = $top_item_result['item_name'] ?? 'N/A';
$top_item_quantity = $top_item_result['total_quantity'] ?? 0;

echo "Calculated metrics for $today_date:<br>";
echo "- Sales: $sales_today<br>";
echo "- Orders: $orders_today<br>";
echo "- Top Item: $top_item_name ($top_item_quantity sold)<br>";

// --- 2. SAVE THE SUMMARY TO THE DATABASE ---

// This is a professional "UPSERT" query.
// It will INSERT a new row for today. If a row for today already exists, it will UPDATE it instead.
// This makes the script safe to run multiple times a day without creating duplicate data.
$stmt_save = $conn->prepare(
    "INSERT INTO daily_summary (summary_date, total_sales, orders_count, top_item_name, top_item_quantity) 
     VALUES (?, ?, ?, ?, ?)
     ON DUPLICATE KEY UPDATE
     total_sales = VALUES(total_sales),
     orders_count = VALUES(orders_count),
     top_item_name = VALUES(top_item_name),
     top_item_quantity = VALUES(top_item_quantity)"
);

$stmt_save->bind_param("sdisi", $today_date, $sales_today, $orders_today, $top_item_name, $top_item_quantity);

if ($stmt_save->execute()) {
    echo "<br><strong>Successfully saved today's summary to the database!</strong>";
} else {
    echo "<br><strong>Error saving summary: " . $stmt_save->error . "</strong>";
}

$conn->close();
?>