<?php
// This MUST be the very first line to access session data.
session_start();

// --- PHP LOGIC BLOCK ---
// This code will run when the page loads.

// 1. Establish database connection.
$conn = new mysqli('localhost', 'root', '', 'klever_db');
if ($conn->connect_error) {
    die("Database Connection failed: " . $conn->connect_error);
}

// 2. Prepare variables. We set them to null initially.
$order = null;
$order_items = [];
$error_message = '';

// 3. Check if the user has searched for an order code.
if (isset($_GET['order_code']) && !empty($_GET['order_code'])) {
    $order_code = $_GET['order_code'];

    // 4. Find the main order details in the `orders` table.
    $stmt_order = $conn->prepare("SELECT * FROM orders WHERE order_code = ?");
    $stmt_order->bind_param("s", $order_code);
    $stmt_order->execute();
    $result_order = $stmt_order->get_result();

    if ($result_order->num_rows == 1) {
        // --- SUCCESS: We found the order. ---
        $order = $result_order->fetch_assoc();
        
        // 5. Now, fetch all the associated items from the `order_items` table.
        $stmt_items = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
        $stmt_items->bind_param("i", $order['id']);
        $stmt_items->execute();
        $result_items = $stmt_items->get_result();
        
        while ($item = $result_items->fetch_assoc()) {
            $order_items[] = $item; // Add each item to our array
        }
        $stmt_items->close();

    } else {
        // --- FAILURE: No order found with that code. ---
        $error_message = "No order found with code '" . htmlspecialchars($order_code) . "'. Please check the code and try again.";
    }
    $stmt_order->close();
}

$conn->close();
// --- END OF PHP LOGIC BLOCK ---
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Your Order - KLEver</title>
    <!-- Your standard links -->
    <link rel="stylesheet" href="style.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <!-- STYLES FOR THIS PAGE - Inspired by your login form -->
    <style>
        /* Using your dark theme background from login */
        body { 
            background-color:rgb(190, 170, 145); 
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
        }
        /* A main container to center the content */
        .page-wrapper {
            display: flex;
            justify-content: center;
            align-items: flex-start; /* Aligns content to the top */
            min-height: calc(100vh - 80px); /* Full height minus header */
            padding-top: 5rem; /* Space below the header */
        }
        /* The main content box, like your login form */
        .track-container {
            width: 100%;
            max-width: 550px;
            margin-top: 3rem;
            background: #fff;
            color: #333;
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(5, 4, 4, 0.2);
            text-align: center;
        }
        .track-container h2 { font-size: 1.8rem; margin-bottom: 3.5rem;}
        .track-container p.subtitle { color: #666; margin-bottom: 2rem; }

        /* Search Form styling */
        .search-form { display: flex; margin-bottom: 2rem; border-radius: 50px; overflow: hidden; border: 1px solid #ddd; }
        .search-form input { flex-grow: 1; border: none; padding: 0.8rem 1.2rem; font-size: 1rem; outline: none; }
        .search-form button { border: none; background: #803D3B; color: #fff; padding: 0 1.5rem; cursor: pointer; font-size: 1.2rem; }

        /* Result Box styling */
        .order-result { border: 2px dashed #eee; border-radius: 8px; padding: 1.5rem; text-align: left; }
        .order-result h3 { text-align: center; color: #803D3B; margin-bottom: 1.5rem; }

        /* Progress Bar from your original design */
        .progress-bar { display: flex; justify-content: space-between; position: relative; margin: 0 auto 2rem auto; max-width: 400px; }
        .progress-bar::before { content: ''; position: absolute; top: 50%; left: 0; transform: translateY(-50%); width: 100%; height: 3px; background: #e0e0e0; }
        .progress-line { position: absolute; top: 50%; left: 0; transform: translateY(-50%); height: 3px; background: #28a745; width: 0%; }
        .step { display: flex; flex-direction: column; align-items: center; position: relative; }
        .step-icon { width: 30px; height: 30px; border-radius: 50%; background: #fff; border: 3px solid #e0e0e0; color: #e0e0e0; display: flex; justify-content: center; align-items: center; }
        .step-label { font-size: 0.8rem; color: #aaa; margin-top: 0.5rem; }
        .step.active .step-icon { border-color: #28a745; color: #28a745; }
        .step.active .step-label { color: #333; font-weight: 600; }

        /* Order Details list */
        .order-details-list { list-style: none; padding: 0; margin: 1rem 0; }
        .order-details-list li { display: flex; justify-content: space-between; padding: 0.8rem 0; border-bottom: 1px solid #f0f0f0; }
        .total-paid { font-weight: bold; font-size: 1.1rem; text-align: right; margin-top: 1rem; color: #333; }
        .total-paid span { color: #803D3B; }
        
        .error-message { color: #D8000C; font-weight: bold; }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <div class="page-wrapper">
        <div class="track-container">
            <h2>Track Your Order</h2>
            <p class="subtitle">Enter the order code from your confirmation email.</p>

            <form action="track_order.php" method="GET" class="search-form">
                <input type="text" name="order_code" placeholder="e.g., KLE-4122" value="<?php echo isset($_GET['order_code']) ? htmlspecialchars($_GET['order_code']) : ''; ?>" required>
                <button type="submit" title="Search"><i class="fas fa-search"></i></button>
            </form>

            <!-- This PHP block decides whether to show the result or an error message -->
            <?php if ($order): ?>
                <div class="order-result">
                    <h3>Order #<?php echo htmlspecialchars($order['order_code']); ?></h3>
                    
                    <div class="progress-bar">
                        <div class="progress-line" style="width: <?php 
                            if ($order['status'] == 'Preparing') echo '50%'; 
                            elseif ($order['status'] == 'Completed') echo '100%'; 
                            else echo '0%'; 
                        ?>;"></div>
                        <div class="step active">
                            <div class="step-icon"><i class="fas fa-receipt"></i></div>
                            <span class="step-label">Order Placed</span>
                        </div>
                        <div class="step <?php if ($order['status'] == 'Preparing' || $order['status'] == 'Completed') echo 'active'; ?>">
                            <div class="step-icon"><i class="fas fa-utensils"></i></div>
                            <span class="step-label">Preparing</span>
                        </div>
                        <div class="step <?php if ($order['status'] == 'Completed') echo 'active'; ?>">
                            <div class="step-icon"><i class="fas fa-check-circle"></i></div>
                            <span class="step-label">Ready for Pickup</span>
                        </div>
                    </div>

                    <ul class="order-details-list">
                        <?php foreach ($order_items as $item): ?>
                            <li>
                                <span><?php echo htmlspecialchars($item['item_name']); ?> x <?php echo htmlspecialchars($item['quantity']); ?></span>
                                <strong>₹<?php echo number_format($item['price_per_item'] * $item['quantity'], 2); ?></strong>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="total-paid">Total Paid: <span>₹<?php echo number_format($order['total'], 2); ?></span></div>
                </div>
            <?php elseif ($error_message): ?>
                <p class="error-message"><?php echo $error_message; ?></p>
            <?php endif; ?>
        </div>
    </div>
<?php include 'footer.php'; ?>
// Find the </body> tag at the end of track_order.php
// And add this ENTIRE <script> block right before it.

<script>
    // This self-executing function will contain all our logic to avoid conflicts.
    (function() {
        // Find the elements on the page we need to interact with.
        const orderResultBox = document.querySelector('.order-result');
        const progressBarLine = document.querySelector('.progress-line');
        const stepIcons = document.querySelectorAll('.step'); // Get all three steps

        // If there's no order result box on the page, do nothing.
        if (!orderResultBox) {
            return;
        }

        // Get the current order code from the h3 tag.
        const orderCodeElement = orderResultBox.querySelector('h3');
        const orderCode = orderCodeElement ? orderCodeElement.textContent.replace('Order #', '').trim() : null;

        if (!orderCode) {
            return;
        }

        // This function updates the UI based on the new status.
        function updateOrderStatusUI(status) {
            let progressPercentage = '0%';
            // De-activate all steps first
            stepIcons.forEach(step => step.classList.remove('active'));

            // Activate steps based on the status
            if (status === 'Pending' || status === 'Preparing' || status === 'Completed') {
                stepIcons[0].classList.add('active'); // Order Placed
            }
            if (status === 'Preparing' || status === 'Completed') {
                stepIcons[1].classList.add('active'); // Preparing
                progressPercentage = '50%';
            }
            if (status === 'Completed') {
                stepIcons[2].classList.add('active'); // Ready for Pickup
                progressPercentage = '100%';
            }
            
            // Animate the green progress bar line.
            if(progressBarLine) {
                progressBarLine.style.width = progressPercentage;
            }
        }

        // This is the main polling function.
        function pollOrderStatus() {
            fetch(`get_order_status.php?order_code=${orderCode}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status && data.status !== 'Error' && data.status !== 'Not Found') {
                        // If we get a valid status, update the UI.
                        updateOrderStatusUI(data.status);
                    }
                })
                .catch(error => console.error('Error polling for status:', error));
        }

        // Start the polling!
        // It will run the pollOrderStatus function every 10000 milliseconds (10 seconds).
        setInterval(pollOrderStatus, 10000);

    })(); // End of self-executing function
</script>
</body>
</html>
