<?php
session_start();

// Security Check: If no user is logged in, redirect to the login page.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$conn = new mysqli('localhost', 'root', '', 'klever_db');
if ($conn->connect_error) { 
    die("Connection Failed: " . $conn->connect_error); 
}

// This query is now fully corrected to use your column names.
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_time DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Orders - KLEver</title>
    <link rel="stylesheet" href="style.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* --- STYLING IMPROVEMENTS --- */
        /* A soft, professional background consistent with your admin theme */
        body { background-color:rgb(215, 198, 196); }
        .page-wrapper { padding: 120px 0 50px 0; min-height: 80vh; }
        .orders-container { 
            max-width: 900px; 
            margin: 0 auto; 
            background: #fff; 
            padding: 2rem; 
            border-radius: 8px; 
            box-shadow: 0 5px 20px rgba(0,0,0,0.08); 
        }
        .orders-container h1 { 
            margin-top: 0; 
            margin-bottom: 2rem; 
            text-align: center; 
            color: #322C2B; /* Dark theme color */
        }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #f0f0f0; }
        th { 
            background-color: #AF8260; /* Header color from your admin theme */
            color: white;
            text-transform: uppercase;
            font-size: 0.8rem;
        }
        .status-pending { color: #d9822b; font-weight: bold; }
        .status-preparing { color: #007bff; font-weight: bold; }
        .status-completed { color: #28a745; font-weight: bold; }
        .btn-track { font-size: 0.9rem; padding: 5px 12px; }
        .actions { display: flex; gap: 10px; }
        .btn-secondary { background-color: #6c757d; color: white; border: none; padding: 5px 12px; font-size: 0.9rem; border-radius: 5px; cursor: pointer; }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="page-wrapper">
        <div class="container orders-container">
            <h1>My Order History</h1>
            <?php if ($result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr><th>Order Code</th><th>Date</th><th>Total</th><th>Status</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php while($order = $result->fetch_assoc()): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($order['order_code']); ?></strong></td>
                                <!-- THE FIX #1: Use 'order_time' instead of 'created_at' -->
                                <td><?php echo date('d M Y, h:i A', strtotime($order['order_time'])); ?></td>
                                <!-- THE FIX #2: Use 'total' instead of 'total_price' -->
                                <td>₹<?php echo number_format($order['total'], 2); ?></td>
                                <td><span class="status-<?php echo strtolower($order['status']); ?>"><?php echo htmlspecialchars($order['status']); ?></span></td>
                                <td class="actions">
                                    <a href="track_order.php?order_code=<?php echo $order['order_code']; ?>" class="btn-primary btn-track">Track</a>
                                    <?php if ($order['status'] == 'Completed'): ?>
                                        <button class="btn-secondary btn-reorder" data-order-id="<?php echo $order['id']; ?>">Reorder</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="text-align:center;">You haven't placed any orders yet. <a href="menu.php">Start your first order!</a></p>
            <?php endif; ?>
        </div>
    </div>
    <?php include 'footer.php'; ?>
    
    <!-- The JavaScript for the Reorder button remains the same and is correct -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const reorderButtons = document.querySelectorAll('.btn-reorder');
        reorderButtons.forEach(button => {
            button.addEventListener('click', function() {
                const orderId = this.dataset.orderId;
                this.textContent = 'Loading...';
                this.disabled = true;
                fetch(`reorder_handler.php?id=${orderId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            localStorage.removeItem('orderItems');
                            localStorage.removeItem('orderTotal');
                            let newCartItems = [];
                            let newTotal = 0;
                            data.items.forEach(item => {
                                const cost = item.quantity * item.price_per_item;
                                newCartItems.push({
                                    id: 'item_' + item.item_id,
                                    name: item.item_name,
                                    quantity: item.quantity,
                                    price: item.price_per_item,
                                    cost: cost
                                });
                                newTotal += cost;
                            });
                            localStorage.setItem('orderItems', JSON.stringify(newCartItems));
                            localStorage.setItem('orderTotal', newTotal);
                            window.location.href = 'cart.php';
                        } else {
                            alert('Could not reorder: ' + data.message);
                            this.textContent = 'Reorder';
                            this.disabled = false;
                        }
                    })
                    .catch(error => {
                        console.error('Reorder error:', error);
                        this.textContent = 'Reorder';
                        this.disabled = false;
                    });
            });
        });
    });
    </script>
</body>
</html>