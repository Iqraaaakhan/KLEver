<?php
session_start();

// Security Check
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

// Check if the form was submitted.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Connect to the database.
    $conn = new mysqli('localhost', 'root', '', 'klever_db');
    if ($conn->connect_error) { 
        die("Connection Failed: " . $conn->connect_error); 
    }

    // 2. Get all the data from the form, including the hidden ID.
    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $image_url = $_POST['image_url'];
    $is_available = $_POST['is_available'];
    $is_featured = $_POST['is_featured'];

    // 3. Prepare a secure UPDATE statement.
    $stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, image_url = ?, is_available = ?, is_featured = ? WHERE id = ?");
    // 'sdsiii' corresponds to the data types: String, Decimal, String, Int, Int, Int
    $stmt->bind_param("sdsiii", $name, $price, $image_url, $is_available, $is_featured, $id);

    // 4. Execute the statement.
    if ($stmt->execute()) {
        // Success! Redirect back to the main menu list.
        header("Location: admin_menu.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

} else {
    // Redirect away if accessed directly.
    header("Location: admin_menu.php");
    exit;
}
?>