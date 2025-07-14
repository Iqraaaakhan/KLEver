<?php // update_status.php
session_start();
// Security check: only admins can update status
if (!isset($_SESSION['admin_logged_in'])) { 
    exit('Access Denied'); 
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['order_id']) && isset($_POST['new_status'])) {
    $conn = new mysqli('localhost', 'root', '', 'klever_db');
    
    $order_id = intval($_POST['order_id']);
    $new_status = $conn->real_escape_string($_POST['new_status']);

    // Whitelist of allowed statuses for extra security
    $allowed_statuses = ['Pending', 'Preparing', 'Completed'];
    if (in_array($new_status, $allowed_statuses)) {
        // Prepare and execute the update query
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $new_status, $order_id);
        $stmt->execute();
        $stmt->close();
    }
    $conn->close();
}

// After updating, always redirect back to the dashboard
header("Location: admin_dashboard.php");
exit;
?>