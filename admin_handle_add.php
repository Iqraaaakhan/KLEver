<?php
session_start();

// Security Check: Only admins can perform this action.
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

// Check if the form was submitted using the POST method.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Connect to the database.
    $conn = new mysqli('localhost', 'root', '', 'klever_db');
    if ($conn->connect_error) { 
        die("Connection Failed: " . $conn->connect_error); 
    }

    // 2. Get the data from the form.
    $name = $_POST['name'];
    $price = $_POST['price'];
    $image_url = $_POST['image_url'];
    $is_available = $_POST['is_available'];
    $is_featured = $_POST['is_featured'];

    // 3. Prepare a secure SQL statement to prevent SQL injection.
    $stmt = $conn->prepare("INSERT INTO products (name, price, image_url, is_available, is_featured) VALUES (?, ?, ?, ?, ?)");
    // 'sdsii' tells the database the types of data: String, Decimal, String, Integer, Integer
    $stmt->bind_param("sdsii", $name, $price, $image_url, $is_available, $is_featured);

    // 4. Execute the statement and check for success.
    if ($stmt->execute()) {
        // Success! Redirect back to the menu management page.
        header("Location: admin_menu.php");
        exit;
    } else {
        // If it fails, show an error.
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

} else {
    // If someone tries to access this page directly, redirect them away.
    header("Location: admin_menu.php");
    exit;
}
?>