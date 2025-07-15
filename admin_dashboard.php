<?php // admin_dashboard.php
session_start();

// Line 5: SECURITY CHECK - This is the most important part.
// It checks if the user is a logged-in admin. If not, it sends them back to the login page.
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

// Line 11: Connect to the database to get the orders.
$conn = new mysqli('localhost', 'root', '', 'klever_db');
if ($conn->connect_error) { 
    die("Connection Failed: " . $conn->connect_error); 
}

// Line 16: Fetch all orders from the database, showing the newest ones at the top.
$orders_result = $conn->query("SELECT * FROM orders ORDER BY order_time DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - KLEver</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Lora:wght@700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="admin_dashboard_styles.css">
</head>
<body>
<div class="admin-wrapper">
   <?php include 'admin_sidebar.php'; ?>
    <!-- Line 73: The Main Content Area for the Order List -->
    <div class="main-content">
        <h1>Dashboard</h1>
        <h2>Recent Orders</h2>
        <table>
            <thead>
                <tr>
                    <th>Order Code</th><th>Customer</th><th>Items</th><th>Total</th><th>Time</th><th>Status</th><th>Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Line 82: Check if there are any orders -->
                <?php if ($orders_result->num_rows > 0): ?>
                    <!-- Line 84: Loop through each order and display it as a table row -->
                    <?php while($order = $orders_result->fetch_assoc()): ?>
                    <tr>
                        <td><strong><?php echo $order['order_code']; ?></strong></td>
                        <td><?php echo htmlspecialchars($order['name']); ?></td>
                        <td>
    <ul>
    <?php 
        // Create a new prepared statement to fetch items for THIS specific order
        $stmt_items = $conn->prepare("SELECT item_name, quantity FROM order_items WHERE order_id = ?");
        $stmt_items->bind_param("i", $order['id']);
        $stmt_items->execute();
        $items_result = $stmt_items->get_result();
        
        while ($item = $items_result->fetch_assoc()) {
            echo "<li>" . htmlspecialchars($item['item_name']) . " x " . $item['quantity'] . "</li>";
        }
        $stmt_items->close();
    ?>
    </ul>
</td>
                        <td>₹<?php echo number_format($order['total'], 2); ?></td>
                        <td><?php echo date("g:i a, d M", strtotime($order['order_time'])); ?></td>
                        <td class="status-<?php echo strtolower($order['status']); ?>"><?php echo $order['status']; ?></td>
                        <td>
                            <!-- Line 108: This form allows the admin to update the status of each order -->
                            <form class="update-form" action="update_status.php" method="POST">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <select name="new_status">
                                    <option value="Pending" <?php if($order['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                                    <option value="Preparing" <?php if($order['status'] == 'Preparing') echo 'selected'; ?>>Preparing</option>
                                    <option value="Completed" <?php if($order['status'] == 'Completed') echo 'selected'; ?>>Completed</option>
                                </select>
                                <button type="submit">Update</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <!-- Line : If there are no orders, show a friendly message -->
                    <tr><td colspan="7" style="text-align:center;">No orders yet. Check back soon!</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    // This function will run every 15 seconds to check for new orders.
    function checkForNewOrders() {
        // Find the very first order row in the table.
        const firstOrderRow = document.querySelector('tbody tr');
        
        // If there are no orders on the page yet, we can't check for new ones.
        if (!firstOrderRow) {
            return; 
        }

        // Get the ID of the latest order currently displayed on the page.
// This version correctly finds the input field inside the form within the row.
const latestOrderId = firstOrderRow.querySelector('.update-form input[name="order_id"]').value;        
        // Call our new PHP script in the background.
        fetch(`admin_check_orders.php?last_id=${latestOrderId}`)
            .then(response => response.json())
            .then(data => {
                // The PHP script sends back { "new_order": true } or { "new_order": false }
                if (data.new_order) {
                    // --- A NEW ORDER HAS ARRIVED! ---
                    
                    // 1. Play the notification sound.
                    const notificationSound = new Audio('assets/notification.mp3');
                    notificationSound.play();
                    
                    // 2. Display a prominent banner at the top of the page.
                    showNewOrderBanner();
                }
            })
            .catch(error => console.error('Error checking for new orders:', error));
    }

    // This function creates and shows the "New Order!" banner.
    function showNewOrderBanner() {
        // Check if a banner already exists to avoid creating multiple.
        if (document.querySelector('.new-order-banner')) {
            return;
        }

        const banner = document.createElement('div');
        banner.className = 'new-order-banner';
        banner.innerHTML = `
            <i class="fas fa-bell"></i>
            <strong>New Order Received!</strong>
            <a href="admin_dashboard.php">Click here to refresh the page</a>
        `;
        
        // Add the banner to the top of the main content area.
        const mainContent = document.querySelector('.main-content');
        mainContent.insertBefore(banner, mainContent.firstChild);
    }

    // This is the main timer. It calls the checkForNewOrders function every 15000 milliseconds (15 seconds).
    setInterval(checkForNewOrders, 15000);
</script>
</body>
</html>