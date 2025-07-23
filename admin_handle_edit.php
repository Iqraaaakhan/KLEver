<?php
session_start();

// Security Check: Ensure an admin is logged in.
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

// Ensure the form was submitted via POST.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. DATABASE CONNECTION
    $conn = new mysqli('localhost', 'root', '', 'klever_db');
    if ($conn->connect_error) {
        die("Connection Failed: " . $conn->connect_error);
    }

    // 2. GET DATA FROM THE FORM
    // These names match your form's 'name' attributes exactly.
    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $image_url = $_POST['image_url'];
    $is_available = $_POST['is_available']; // Reads '1' or '0'
    $is_featured = $_POST['is_featured'];   // Reads '1' or '0'

    // NOTE: We do NOT touch 'is_active' here. That is handled by Delete/Restore.

    // 3. PREPARE AND EXECUTE THE DATABASE UPDATE
    $stmt = $conn->prepare(
        "UPDATE products SET name = ?, price = ?, image_url = ?, is_available = ?, is_featured = ? WHERE id = ?"
    );
    
    // Bind the variables to the query
    $stmt->bind_param("sdsiii", $name, $price, $image_url, $is_available, $is_featured, $id);

    // Execute the query and set a feedback message
    if ($stmt->execute()) {
        $_SESSION['flash_message'] = "Item '" . htmlspecialchars($name) . "' updated successfully!";
    } else {
        $_SESSION['flash_message'] = "Error updating item: " . $stmt->error;
    }

    // 4. CLEAN UP AND REDIRECT
    $stmt->close();
    $conn->close();

    header("Location: admin_menu.php");
    exit;

} else {
    // Redirect if the page is accessed directly.
    header("Location: admin_menu.php");
    exit;
}
?>