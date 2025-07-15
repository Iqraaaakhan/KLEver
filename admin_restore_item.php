<?php
session_start();

// Security Check
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

if (isset($_GET['id'])) {
    $conn = new mysqli('localhost', 'root', '', 'klever_db');
    if ($conn->connect_error) { die("Connection Failed: " . $conn->connect_error); }

    $item_id = $_GET['id'];

    // This is the opposite of delete: We set is_active back to 1.
    $stmt = $conn->prepare("UPDATE products SET is_active = 1 WHERE id = ?");
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    
    $stmt->close();
    $conn->close();
}

// Redirect back to the menu page to see the change.
header("Location: admin_menu.php");
exit;
?>