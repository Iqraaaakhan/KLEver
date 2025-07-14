<?php // track_order.php - FINAL POLISHED VERSION
session_start();
$order = null;
$error_message = '';

if (isset($_GET['order_code']) && !empty($_GET['order_code'])) {
    $conn = new mysqli('localhost', 'root', '', 'klever_db');
    if ($conn->connect_error) {
        $error_message = "Could not connect to the database.";
    } else {
        $order_code = $conn->real_escape_string($_GET['order_code']);
        $stmt = $conn->prepare("SELECT * FROM orders WHERE order_code = ?");
        $stmt->bind_param("s", $order_code);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $order = $result->fetch_assoc();
        } else {
            $error_message = "Sorry, we couldn't find an order with that code.";
        }
        $stmt->close();
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Your Order - KLEver</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body { background-color:rgb(143, 140, 138); display: flex; flex-direction: column; min-height: 100vh; }
        .track-page-container { flex: 1; display: flex; justify-content: center; align-items: center; padding: 2rem; }
        .track-box { background: #fff; padding: 2.5rem; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); width: 100%; max-width: 600px; text-align: center; }
        .track-box h1 { font-size: 2rem; color: #322C2B; margin-bottom: 1rem; }
        .track-box p { color: #666; margin-bottom: 2rem; }
        
        .track-form { display: flex; border: 2px solid #ddd; border-radius: 50px; overflow: hidden; margin-bottom: 2rem; }
        .track-form input { flex-grow: 1; border: none; padding: 1rem 1.5rem; font-size: 1rem; outline: none; }
        .track-form button { border: none; background: #803D3B; color: white; padding: 0 2rem; font-size: 1.2rem; cursor: pointer; transition: background-color 0.3s; }
        .track-form button:hover { background: #322C2B; }

        .error-msg { color: #D8000C; background-color: #FFD2D2; padding: 1rem; border-radius: 8px; margin-bottom: 2rem; }

        .status-display { border: 2px dashed #eee; border-radius: 10px; padding: 2rem; }
        .status-display h2 { margin-top: 0; color: #AF8260; font-size: 1.5rem; }
        
        /* 3-Stage Progress Bar */
        .status-visualizer { display: flex; justify-content: space-between; position: relative; margin: 2rem 0; }
        .status-visualizer::before { content: ''; position: absolute; top: 50%; left: 10%; right: 10%; height: 4px; background: #e0e0e0; transform: translateY(-50%); z-index: 1; }
        .progress-bar { position: absolute; top: 50%; left: 10%; height: 4px; background: #28a745; transform: translateY(-50%); z-index: 2; width: 0%; transition: width 0.5s ease; }

        .step { z-index: 3; position: relative; text-align: center; width: 80px; }
        .step-icon { width: 40px; height: 40px; background: #e0e0e0; border-radius: 50%; display: flex; justify-content: center; align-items: center; color: white; font-size: 1.2rem; margin: 0 auto 0.5rem auto; transition: background-color 0.5s ease; border: 3px solid #f4f1ee; }
        .step-label { font-weight: 600; color: #aaa; transition: color 0.5s ease; font-size: 0.9rem; }
        
        /* Active (Orange) Step Styles */
        .step.active .step-icon { background: #d9822b; border-color: #fff; }
        .step.active .step-label { color: #d9822b; }

        /* Completed (Green) Step Styles */
        .step.completed .step-icon { background: #28a745; border-color: #fff; }
        .step.completed .step-label { color: #28a745; }

        /* Expandable Details Section */
        .details-toggle { margin-top: 2rem; font-weight: 600; color: #6b4f4f; cursor: pointer; display: inline-block; padding: 0.5rem 1rem; border-radius: 20px; transition: background-color 0.3s; }
        .details-toggle:hover { background-color: #f0f0f0; }
        .details-toggle i { margin-left: 0.5rem; transition: transform 0.3s ease; }
        .details-toggle i.fa-chevron-up { transform: rotate(180deg); }

        .order-details-content { max-height: 0; overflow: hidden; transition: max-height 0.5s ease-out; text-align: left; margin-top: 1rem; border-top: 1px solid #eee; }
        .order-details-content.show { max-height: 500px; }
        .order-details-content ul { list-style: none; padding: 1rem 0 0 0; }
        .order-details-content li { display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #f5f5f5; }
        .order-details-content li span:first-child { font-weight: 600; }
        .details-total { text-align: right; font-size: 1.1rem; margin-top: 1rem; color: #322C2B; }

    </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="track-page-container">
    <div class="track-box">
        <h1>Track Your Order</h1>
        <p>Enter the order code you received on your confirmation page.</p>

        <form action="track_order.php" method="GET" class="track-form">
            <input type="text" name="order_code" placeholder="e.g., KLE-3055" value="<?php echo htmlspecialchars($order['order_code'] ?? ''); ?>">
            <button type="submit"><i class="fas fa-search"></i></button>
        </form>

        <?php if ($error_message): ?>
            <div class="error-msg"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <?php if ($order): ?>
            <div id="status-display" class="status-display" data-order-code="<?php echo $order['order_code']; ?>">
                <h2>Order #<?php echo htmlspecialchars($order['order_code']); ?></h2>
                <div class="status-visualizer">
                    <div class="step" id="step-pending"><div class="step-icon"><i class="fas fa-receipt"></i></div><div class="step-label">Order Placed</div></div>
                    <div class="step" id="step-preparing"><div class="step-icon"><i class="fas fa-utensil-spoon"></i></div><div class="step-label">Preparing</div></div>
                    <div class="step" id="step-completed"><div class="step-icon"><i class="fas fa-utensils"></i></div><div class="step-label">Ready for Pickup</div></div>
                    <div class="progress-bar" id="progress-bar"></div>
                </div>

                <div class="details-toggle" onclick="toggleDetails()">
                    Show Order Details <i class="fas fa-chevron-down"></i>
                </div>
                <div class="order-details-content">
                    <ul>
                        <?php 
                            $items = json_decode($order['order_details'], true);
                            if (is_array($items)) {
                                foreach($items as $item) {
                                    echo "<li><span>" . htmlspecialchars($item['name']) . "</span><span>x " . $item['quantity'] . "</span></li>";
                                }
                            }
                        ?>
                    </ul>
                    <div class="details-total">
                        Total Paid: <strong>₹<?php echo number_format($order['total'], 2); ?></strong>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if ($order): ?>
<script>
    function toggleDetails() {
        document.querySelector('.order-details-content').classList.toggle('show');
        document.querySelector('.details-toggle i').classList.toggle('fa-chevron-up');
    }

    const orderCode = document.getElementById('status-display').dataset.orderCode;
    
    function updateStatusVisual(status) {
        const stepPending = document.getElementById('step-pending');
        const stepPreparing = document.getElementById('step-preparing');
        const stepCompleted = document.getElementById('step-completed');
        const progressBar = document.getElementById('progress-bar');

        stepPending.classList.remove('active', 'completed');
        stepPreparing.classList.remove('active', 'completed');
        stepCompleted.classList.remove('active', 'completed');
        
        if (status === 'Pending') {
            stepPending.classList.add('active');
            progressBar.style.width = '0%';
        } else if (status === 'Preparing') {
            stepPending.classList.add('completed');
            stepPreparing.classList.add('active');
            progressBar.style.width = '50%';
        } else if (status === 'Completed') {
            stepPending.classList.add('completed');
            stepPreparing.classList.add('completed');
            stepCompleted.classList.add('completed');
            progressBar.style.width = '100%';
        }
    }

    async function checkStatus() {
        try {
            const response = await fetch(`get_status.php?order_code=${orderCode}`);
            const data = await response.json();
            if (data.status) {
                updateStatusVisual(data.status);
            }
        } catch (error) {
            console.error("Failed to fetch status:", error);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        updateStatusVisual('<?php echo $order['status']; ?>');
        setInterval(checkStatus, 7000); 
    });
</script>
<?php endif; ?>

</body>
</html>