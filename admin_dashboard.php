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
  <style>
    /* This CSS creates a professional two-column layout */
    body { background-color: #f4f7f6; font-family: 'Poppins', sans-serif; margin: 0; }
    .admin-wrapper { display: flex; }
    .sidebar { width: 250px; background: #322C2B; color: #E4C59E; min-height: 100vh; position: fixed; display: flex; flex-direction: column; }
    .sidebar-header { text-align: center; padding: 1.5rem 0; margin: 0; font-family: 'Lora', serif; border-bottom: 1px solid #444; font-size: 1.8rem; }
    .sidebar-nav { list-style: none; padding: 0; margin-top: 1rem; }
    .sidebar-nav li a { color: #E4C59E; text-decoration: none; display: flex; align-items: center; padding: 1rem; transition: background-color 0.3s, color 0.3s; font-size: 1rem; }
    .sidebar-nav li.active a, .sidebar-nav li a:hover { background-color: #803D3B; color: #fff; }
    .sidebar-nav li a i { margin-right: 1rem; width: 20px; text-align: center; }
    .logout-link { margin-top: auto; border-top: 1px solid #444; } /* Pushes logout to the bottom */
    
    .main-content { margin-left: 250px; /* Makes space for the fixed sidebar */ flex-grow: 1; padding: 2rem; }
    .main-content h1 { margin-top: 0; color: #322C2B; }
    table { width: 100%; border-collapse: collapse; box-shadow: 0 4px 15px rgba(0,0,0,0.08); background: #fff; border-radius: 8px; overflow: hidden; font-size: 0.9rem; }
    th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #f0f0f0; }
    th { background-color: #AF8260; color: white; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.5px; }
    td ul { padding-left: 20px; margin: 0; }
    .status-pending { color: #d9822b; font-weight: bold; }
    .status-preparing { color: #007bff; font-weight: bold; }
    .status-completed { color: #28a745; font-weight: bold; }
    .update-form { display: flex; align-items: center; gap: 8px; }
    .update-form select, .update-form button { padding: 5px; border-radius: 4px; border: 1px solid #ccc; font-family: 'Poppins', sans-serif; }
    .update-form button { background-color: #803D3B; color: white; cursor: pointer; font-weight: 600; }
  </style>
</head>
<body>
<div class="admin-wrapper">
    <!-- Line 61: The Sidebar Navigation -->
    <div class="sidebar">
        <h2 class="sidebar-header">KLEver</h2>
        <ul class="sidebar-nav">
            <li class="active"><a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i>Dashboard</a></li>
            <li><a href="#"><i class="fas fa-utensils"></i>Menu Management</a></li>
            <li><a href="#"><i class="fas fa-users"></i>Users</a></li>
        </ul>
        <ul class="sidebar-nav logout-link">
             <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a></li>
        </ul>
    </div>

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
                    <!-- Line 122: If there are no orders, show a friendly message -->
                    <tr><td colspan="7" style="text-align:center;">No orders yet. Check back soon!</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>