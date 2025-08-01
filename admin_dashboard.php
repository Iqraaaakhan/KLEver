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
$orders_result = $conn->query("SELECT * FROM orders ORDER BY order_time DESC LIMIT 20");

// --- START: ADD THIS ENTIRE NEW ANALYTICS BLOCK ---

// Get today's date in 'YYYY-MM-DD' format from PHP. This is timezone-safe.
$today_date = date('Y-m-d');

// METRIC 1: Total Sales for Today (using a secure prepared statement)
$stmt_sales = $conn->prepare("SELECT SUM(total) as total_sales FROM orders WHERE DATE(order_time) = ?");
$stmt_sales->bind_param("s", $today_date);
$stmt_sales->execute();
$sales_today = $stmt_sales->get_result()->fetch_assoc()['total_sales'] ?? 0;

// METRIC 2: Number of Orders Today (using a secure prepared statement)
$stmt_orders = $conn->prepare("SELECT COUNT(id) as order_count FROM orders WHERE DATE(order_time) = ?");
$stmt_orders->bind_param("s", $today_date);
$stmt_orders->execute();
$orders_today = $stmt_orders->get_result()->fetch_assoc()['order_count'] ?? 0;

// METRIC 3: Top Selling Item (Most frequently ordered)
$top_item_result = $conn->query(
    "SELECT item_name, SUM(quantity) as total_quantity 
     FROM order_items 
     GROUP BY item_name 
     ORDER BY total_quantity DESC 
     LIMIT 1"
);
$top_item = $top_item_result->fetch_assoc()['item_name'] ?? 'N/A';

// METRIC 4: Total Registered Users (excluding admin)
$total_users_result = $conn->query("SELECT COUNT(id) as user_count FROM user WHERE type != 1");
$total_users = $total_users_result->fetch_assoc()['user_count'] ?? 0;

// --- END: ADD THIS ENTIRE NEW ANALYTICS BLOCK ---

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
    <!-- The Main Content Area for the Order List -->
   <div class="main-content">
   <!-- START: DYNAMIC BANNER HTML -->
<div class="new-order-banner" id="newOrderBanner" style="display: none;">
    <i class="fas fa-bell"></i>
    <span id="newOrderMessage"></span> <!-- Placeholder for our dynamic message -->
</div>
<!-- END: DYNAMIC BANNER HTML -->

    <h1>Dashboard</h1>
    <!-- START: ADD THIS ENTIRE NEW HTML SECTION -->
    <div class="stat-cards-container">
        <div class="stat-card">
            <div class="stat-icon sales">
                <i class="fas fa-rupee-sign"></i>
            </div>
            <div class="stat-info">
                <p>Sales Today</p>
                <h3>₹<?php echo number_format($sales_today, 2); ?></h3>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon orders">
                <i class="fas fa-receipt"></i>
            </div>
            <div class="stat-info">
                <p>Orders Today</p>
                <h3><?php echo $orders_today; ?></h3>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon top-item">
                <i class="fas fa-star"></i>
            </div>
            <div class="stat-info">
                <p>Top Selling Item</p>
                <h3><?php echo htmlspecialchars($top_item); ?></h3>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon users">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <p>Total Users</p>
                <h3><?php echo $total_users; ?></h3>
            </div>
        </div>
    </div>
    <!-- END: ADD THIS ENTIRE NEW HTML SECTION -->

    <table>
            <thead>
                <tr>
                    <th>Order Code</th><th>Customer</th><th>Items</th><th>Total</th><th>Time</th><th>Status</th><th>Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Line: Check if there are any orders -->
                <?php if ($orders_result->num_rows > 0): ?>
                    <!-- Line: Loop through each order and display it as a table row -->
                    <?php while($order = $orders_result->fetch_assoc()): ?>
                    <tr>
<td>
    <a href="admin_order_details.php?id=<?php echo $order['id']; ?>" style="font-weight:bold; text-decoration:underline; color:#322C2B;">
        <?php echo htmlspecialchars($order['order_code']); ?>
    </a>
</td>                        <td><?php echo htmlspecialchars($order['name']); ?></td>
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
                            <!-- Line 154: This form allows the admin to update the status of each order -->
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
    // This variable will hold the timer for the banner.
    let bannerTimeout;
    
    // Create a single, reusable audio object for the notification sound.
    const notificationSound = new Audio('assets/notification.mp3');
    
    // This flag tracks if the user has "unlocked" the audio by clicking.
    let audioUnlocked = false;

    // This function runs only ONCE, the very first time the user clicks anywhere on the page.
    function unlockAudio() {
        if (!audioUnlocked) {
            // We play a tiny, silent sound to get the browser's permission.
            notificationSound.volume = 0;
            notificationSound.play().then(() => {
                // Success! The browser has given us permission.
                notificationSound.pause();
                notificationSound.currentTime = 0;
                notificationSound.volume = 1.0; // Restore volume for actual notifications.
                audioUnlocked = true;
                // We remove this listener because we only need permission once.
                document.removeEventListener('click', unlockAudio);
            }).catch(error => {
                // This might fail if the click is too fast, but it's okay.
                // The main notification will still try to play the sound.
            });
        }
    }
    // We attach the unlock function to the entire document..
    document.addEventListener('click', unlockAudio);


    function checkForNewOrders() {
        const tableBody = document.querySelector('tbody');
        if (!tableBody) return;
        
        const firstOrderRow = tableBody.querySelector('tr');
        let latestOrderId = 0;
        if (firstOrderRow && firstOrderRow.querySelector('.update-form input[name="order_id"]')) {
            latestOrderId = firstOrderRow.querySelector('.update-form input[name="order_id"]').value;
        }
        
        fetch(`admin_check_orders.php?last_id=${latestOrderId}`)
            .then(response => response.json())
            .then(data => {
                if (data.new_order && data.html && data.message) {
                    // --- A NEW ORDER HAS ARRIVED! ---
                    
                    // 1. Play the notification sound. This will now work reliably.
                    notificationSound.play();
                    
                    // 2. Update and show the dynamic notification banner.
                    const banner = document.getElementById('newOrderBanner');
                    const bannerMessage = document.getElementById('newOrderMessage');
                    if (banner && bannerMessage) {
                        bannerMessage.innerHTML = data.message;
                        banner.style.display = 'flex';

                        // 3. Make the banner disappear automatically after 10 seconds.
                        clearTimeout(bannerTimeout);
                        bannerTimeout = setTimeout(() => {
                            banner.style.display = 'none';
                        }, 10000); // 10 seconds
                    }
                    
                    // 4. Insert the new HTML rows at the top of the table.
                    tableBody.insertAdjacentHTML('afterbegin', data.html);
                }
            })
            .catch(error => console.error('Error checking for new orders:', error));
    }

    // This is the main timer. It calls the function every 10000 milliseconds (10 seconds).
    setInterval(checkForNewOrders, 10000);
</script>

</body>
</html>