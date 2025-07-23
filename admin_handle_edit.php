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

    // 2. Get ALL FIVE values from the form, including the hidden ID.
    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $image_url = $_POST['image_url'];
    $is_available = $_POST['is_available']; // The missing value
    $is_featured = $_POST['is_featured'];   // The other missing value

    // 3. Prepare the new, complete, and secure UPDATE statement.
    // This query now includes is_available and is_featured.
    $stmt = $conn->prepare(
        "UPDATE products 
         SET name = ?, price = ?, image_url = ?, is_available = ?, is_featured = ? 
         WHERE id = ?"
    );
    
    // 4. Bind the parameters with the new, correct type string: 'sdsiii'
    // s = string, d = double/decimal, i = integer
    $stmt->bind_param("sdsiii", $name, $price, $image_url, $is_available, $is_featured, $id);

    // 5. Execute the statement and check for success.
    if ($stmt->execute()) {
        // Success! Redirect back to the menu management page to see the changes.
        header("Location: admin_menu.php");
        exit;
    } else {
        // If it fails, show an error.
        echo "Error updating record: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

} else {
    // If someone tries to access this page directly, redirect them away.
    header("Location: admin_menu.php");
    exit;
}
?>