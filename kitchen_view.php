<?php
session_start();
// Security Check from your other admin pages.
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Kitchen Display - KLEver</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Lora:wght@700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="admin_dashboard_styles.css">
</head>
<body>
<div class="admin-wrapper">
    <?php include 'admin_sidebar.php'; ?>

    <div class="main-content">
        <div class="header-bar">
            <h1>Kitchen Order Queue</h1>
        </div>
        
        <div class="kds-container">
            <!-- Column for New Orders -->
            <div class="kds-column" id="pending-col">
                <div class="kds-column-header">
                    <h2><i class="fas fa-receipt"></i> New Orders</h2>
                </div>
                <div class="kds-column-body" id="pending-orders-col">
                    <!-- Order cards will be inserted here by JavaScript -->
                </div>
            </div>

            <!-- Column for Preparing Orders -->
            <div class="kds-column" id="preparing-col">
                <div class="kds-column-header">
                    <h2><i class="fas fa-utensils"></i> In Progress</h2>
                </div>
                <div class="kds-column-body" id="preparing-orders-col">
                    <!-- Order cards will be inserted here by JavaScript -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const pendingColumn = document.getElementById('pending-orders-col');
    const preparingColumn = document.getElementById('preparing-orders-col');
    const notificationSound = new Audio('assets/notification.mp3');
    let currentOrderIds = new Set();

    // Function to update the status via fetch, calling your EXISTING update_status.php
    function handleStatusUpdate(orderId, newStatus) {
        const formData = new FormData();
        formData.append('order_id', orderId);
        formData.append('new_status', newStatus);

        fetch('update_status.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (response.ok) {
                fetchAndRenderOrders(); // Refresh the view immediately after a successful update
            } else {
                alert('Failed to update status.');
            }
        })
        .catch(error => console.error('Error updating status:', error));
    }

    // Main function to fetch data and draw the KDS
    async function fetchAndRenderOrders() {
        try {
            const response = await fetch('get_kitchen_orders.php');
            const orders = await response.json();

            // Clear existing columns
            pendingColumn.innerHTML = '';
            preparingColumn.innerHTML = '';

            const newOrderIds = new Set(orders.map(o => o.id));
            let hasNewOrder = false;

            orders.forEach(order => {
                if (!currentOrderIds.has(order.id)) {
                    hasNewOrder = true;
                }

                const itemsHtml = order.items.map(item => `
                    <li>
                        <strong>${item.quantity} x</strong> ${item.name}
                    </li>
                `).join('');

                const orderCard = document.createElement('div');
                orderCard.className = 'kds-order-card';
                orderCard.dataset.orderId = order.id;

                // Calculate time since order was placed
                const orderTime = new Date(order.order_time);
                const timeDiff = Math.round((new Date() - orderTime) / 60000); // in minutes

                let actionButtonHtml = '';
                if (order.status === 'Pending') {
                    actionButtonHtml = `<button class="btn-kds-action start-prep" data-order-id="${order.id}" data-new-status="Preparing">Start Preparing</button>`;
                } else if (order.status === 'Preparing') {
                    actionButtonHtml = `<button class="btn-kds-action mark-ready" data-order-id="${order.id}" data-new-status="Completed">Mark as Ready</button>`;
                }

                orderCard.innerHTML = `
                    <div class="kds-card-header">
                        <h3>#${order.order_code}</h3>
                        <span><i class="far fa-clock"></i> ${timeDiff} min ago</span>
                    </div>
                    <ul class="kds-item-list">${itemsHtml}</ul>
                    <div class="kds-card-footer">
                        ${actionButtonHtml}
                    </div>
                `;

                if (order.status === 'Pending') {
                    pendingColumn.appendChild(orderCard);
                } else if (order.status === 'Preparing') {
                    preparingColumn.appendChild(orderCard);
                }
            });

            if (hasNewOrder && currentOrderIds.size > 0) {
                notificationSound.play();
            }
            currentOrderIds = newOrderIds;

        } catch (error) {
            console.error('Failed to fetch kitchen orders:', error);
        }
    }

    // Add a single event listener to handle all button clicks
    document.querySelector('.kds-container').addEventListener('click', function(e) {
        if (e.target && e.target.matches('.btn-kds-action')) {
            const orderId = e.target.dataset.orderId;
            const newStatus = e.target.dataset.newStatus;
            handleStatusUpdate(orderId, newStatus);
        }
    });

    // Initial load and start polling every 10 seconds
    fetchAndRenderOrders();
    setInterval(fetchAndRenderOrders, 10000);
});
</script>
</body>
</html>