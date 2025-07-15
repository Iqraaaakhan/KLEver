<?php
session_start();

// Security Check: Only admins can perform this action.
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

// Check if an item ID was provided in the URL.
if (isset($_GET['id'])) {
    
    // 1. Connect to the database.
    $conn = new mysqli('localhost', 'root', '', 'klever_db');
    if ($conn->connect_error) { 
        die("Connection Failed: " . $conn->connect_error); 
    }

    // 2. Get the item ID from the URL.
    $item_id = $_GET['id'];

    // 3. Prepare a secure SQL statement to UPDATE the item.
    // Instead of DELETE, we SET is_active = 0. This is the "soft delete".
    $stmt = $conn->prepare("UPDATE products SET is_active = 0 WHERE id = ?");
    // 'i' tells the database the type is an Integer.
    $stmt->bind_param("i", $item_id);

    // 4. Execute the statement.
    if ($stmt->execute()) {
        // Success! Redirect back to the menu management page.
        // You will see the item has instantly disappeared from the list.
        header("Location: admin_menu.php");
        exit;
    } else {
        // If it fails, show an error.
        echo "Error: Could not delete item.";
    }

    $stmt->close();
    $conn->close();

} else {
    // If no ID was provided, just redirect back.
    header("Location: admin_menu.php");
    exit;
}
?>