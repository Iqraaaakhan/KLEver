<?php
// We must start the session to access the admin login state.
session_start();

// SECURITY CHECK: This is the most important part.
// If the user is not a logged-in admin, redirect them away.
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

// 1. Connect to the database.
$conn = new mysqli('localhost', 'root', '', 'klever_db');
if ($conn->connect_error) { 
    die("Connection Failed: " . $conn->connect_error); 
}

// 2. Fetch all menu items from the database.
// We order them by name for easy viewing.
$products_result = $conn->query("SELECT * FROM products ORDER BY name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Menu Management - Admin Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- We will use the exact same CSS and Fonts as your main dashboard for a consistent look -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Lora:wght@700&display=swap" rel="stylesheet">
  <!-- Re-using your existing admin dashboard stylesheet -->
  <link rel="stylesheet" href="admin_dashboard_styles.css"> <!-- Assuming your dashboard CSS is in this file -->
  
  <!-- Some additional styles for this specific page -->
  <style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }
    .btn-add-new {
        background-color: #28a745; /* A nice green for "Add" */
        color: white;
        padding: 0.7rem 1.2rem;
        text-decoration: none;
        border-radius: 5px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    .btn-add-new:hover {
        background-color: #218838;
    }
    .item-image {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
    }
    .actions a {
        margin-right: 10px;
        text-decoration: none;
        color: #007bff;
    }
    .actions a.delete {
        color: #dc3545;
    }
    .status-available { color: #28a745; font-weight: bold; }
    .status-unavailable { color: #dc3545; font-weight: bold; }
  </style>
</head>
<body>
<div class="admin-wrapper">
    <!-- Include the exact same sidebar for consistent navigation -->
    <?php include 'admin_sidebar.php'; ?>

    <!-- Main Content Area -->
    <div class="main-content">
        <div class="page-header">
            <h1>Menu Management</h1>
            <!-- This button will eventually link to admin_add_item.php -->
            <a href="#" class="btn-add-new"><i class="fas fa-plus"></i> Add New Item</a>
        </div>

        <p>Here you can view, add, edit, and manage all the food items available in the canteen.</p>
        
        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Item Name</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($products_result->num_rows > 0): ?>
                    <?php while($item = $products_result->fetch_assoc()): ?>
                    <tr>
                        <td><img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="item-image"></td>
                        <td><strong><?php echo htmlspecialchars($item['name']); ?></strong></td>
                        <td>₹<?php echo number_format($item['price'], 2); ?></td>
                        <td>
                            <?php if ($item['is_available']): ?>
                                <span class="status-available">Available</span>
                            <?php else: ?>
                                <span class="status-unavailable">Unavailable</span>
                            <?php endif; ?>
                        </td>
                        <td class="actions">
                            <!-- These buttons are for the UI; they are not functional yet -->
                            <a href="#" title="Edit Item"><i class="fas fa-edit"></i> Edit</a>
                            <a href="#" class="delete" title="Delete Item"><i class="fas fa-trash-alt"></i> Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" style="text-align:center;">No menu items found. Click "Add New Item" to get started.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>