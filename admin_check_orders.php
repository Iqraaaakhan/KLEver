<?php
session_start();
header('Content-Type: application/json');

// Security Check remains the same
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['new_order' => false]);
    exit;
}

$last_known_id = isset($_GET['last_id']) ? (int)$_GET['last_id'] : 0;
$conn = new mysqli('localhost', 'root', '', 'klever_db');
if ($conn->connect_error) { 
    echo json_encode(['new_order' => false]);
    exit; 
}

// The query now fetches the full details of all new orders
$stmt = $conn->prepare("SELECT * FROM orders WHERE id > ? ORDER BY order_time DESC");
$stmt->bind_param("i", $last_known_id);
$stmt->execute();
$new_orders_result = $stmt->get_result();

if ($new_orders_result->num_rows > 0) {
    $html = '';
    // We loop through each new order and build its HTML table row
    while ($order = $new_orders_result->fetch_assoc()) {
        $html .= '<tr class="new-order-row">'; // Add a class for animation
        $html .= '<td><a href="admin_order_details.php?id=' . $order['id'] . '" style="font-weight:bold; text-decoration:underline; color:#322C2B;">' . htmlspecialchars($order['order_code']) . '</a></td>';
        $html .= '<td>' . htmlspecialchars($order['name']) . '</td>';
        $html .= '<td><ul>';
        
        $stmt_items = $conn->prepare("SELECT item_name, quantity FROM order_items WHERE order_id = ?");
        $stmt_items->bind_param("i", $order['id']);
        $stmt_items->execute();
        $items_result = $stmt_items->get_result();
        while ($item = $items_result->fetch_assoc()) {
            $html .= "<li>" . htmlspecialchars($item['item_name']) . " x " . $item['quantity'] . "</li>";
        }
        $stmt_items->close();

        $html .= '</ul></td>';
        $html .= '<td>₹' . number_format($order['total'], 2) . '</td>';
        $html .= '<td>' . date("g:i a, d M", strtotime($order['order_time'])) . '</td>';
        $html .= '<td class="status-' . strtolower($order['status']) . '">' . $order['status'] . '</td>';
        $html .= '<td><form class="update-form" action="update_status.php" method="POST"><input type="hidden" name="order_id" value="' . $order['id'] . '"><select name="new_status"><option value="Pending" ' . ($order['status'] == 'Pending' ? 'selected' : '') . '>Pending</option><option value="Preparing" ' . ($order['status'] == 'Preparing' ? 'selected' : '') . '>Preparing</option><option value="Completed" ' . ($order['status'] == 'Completed' ? 'selected' : '') . '>Completed</option></select><button type="submit">Update</button></form></td>';
        $html .= '</tr>';
    }
    // Instead of just true/false, we send back the generated HTML
    echo json_encode(['new_order' => true, 'html' => $html]);
} else {
    echo json_encode(['new_order' => false]);
}

$stmt->close();
$conn->close();
?>