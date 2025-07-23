<?php
session_start();

// Security check
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

// Check if a valid order ID was provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: admin_dashboard.php");
    exit;
}

$order_id = $_GET['id'];
$conn = new mysqli('localhost', 'root', '', 'klever_db');
if ($conn->connect_error) { 
    die("Connection Failed: " . $conn->connect_error); 
}

// Fetch the main order details using the ID
$stmt_order = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt_order->bind_param("i", $order_id);
$stmt_order->execute();
$order_result = $stmt_order->get_result();

// If no order is found, redirect back
if ($order_result->num_rows == 0) {
    header("Location: admin_dashboard.php");
    exit;
}
$order = $order_result->fetch_assoc();

// Fetch all the items associated with this order
$stmt_items = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$items_result = $stmt_items->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Order Details - KLEver Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="admin_dashboard_styles.css"> 
  <style>
    .order-summary-card { background: #fff; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
    .order-summary-card h3 { margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 1rem; margin-bottom: 1rem; }
    .order-summary-card p { margin: 0.5rem 0; color: #555; }
    .order-summary-card strong { color: #333; }
    tfoot td { font-weight: bold; font-size: 1.1rem; }
  </style>
</head>
<body>
<div class="admin-wrapper">
    <?php include 'admin_sidebar.php'; ?>
    <div class="main-content">
        <div class="header-bar">
            <h1>Order Details</h1>
            <a href="admin_dashboard.php" class="btn btn-cancel"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        </div>
        
        <div class="order-summary-card">
            <h3>Order #<?php echo htmlspecialchars($order['order_code']); ?></h3>
            <p><strong>Customer Name:</strong> <?php echo htmlspecialchars($order['name']); ?></p>
            <p><strong>Customer Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
            <p><strong>Order Time:</strong> <?php echo date('d M Y, h:i A', strtotime($order['order_time'])); ?></p>
            <p><strong>Current Status:</strong> <span class="status-<?php echo strtolower($order['status']); ?>"><?php echo htmlspecialchars($order['status']); ?></span></p>
        </div>

        <h2 style="margin-top: 2rem;">Items in this Order</h2>
        <table>
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Quantity</th>
                    <th>Price Per Item</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php while($item = $items_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td>₹<?php echo number_format($item['price_per_item'], 2); ?></td>
                    <td>₹<?php echo number_format($item['price_per_item'] * $item['quantity'], 2); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="text-align:right;">Total Paid:</td>
                    <td>₹<?php echo number_format($order['total'], 2); ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
</body>
</html>