<?php
session_start();

// SECURITY CHECK: This is critical. Redirect any non-admin away immediately.
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

// 1. Connect to the database.
$conn = new mysqli('localhost', 'root', '', 'klever_db');
if ($conn->connect_error) { 
    die("Connection Failed: " . $conn->connect_error); 
}

// 2. Fetch all products from your master 'products' table.
$products_result = $conn->query("SELECT * FROM products ORDER BY name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Menu Management - KLEver Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- We use the exact same links as your dashboard for a consistent look -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Lora:wght@700&display=swap" rel="stylesheet">
  <!-- This links to your existing admin stylesheet -->
  <link rel="stylesheet" href="admin_dashboard_styles.css"> 
</head>
<body>
<div class="admin-wrapper">
    
    <?php 
    // This includes your existing sidebar for consistent navigation.
    // The sidebar will correctly highlight "Menu Management" as the active page.
    include 'admin_sidebar.php'; 
    ?>

    <!-- Main Content Area -->
    <div class="main-content">
        <div class="header-bar">
            <h1>Menu Management</h1>
            <!-- This button will be made functional in our next step -->
<a href="admin_add_item.php" class="btn btn-success"><i class="fas fa-plus"></i> Add New Item</a>
        </div>
        
        <p class="page-description">Here you can view, add, edit, and manage all the food items available in the canteen.</p>

        <!-- This table has the exact same structure as your screenshot -->
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
                    <!-- The PHP loop starts here -->
                    <?php while($product = $products_result->fetch_assoc()): ?>
                    <tr>
                        <td><img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="item-image"></td>
                        <td><strong><?php echo htmlspecialchars($product['name']); ?></strong></td>
                        <td>₹<?php echo number_format($product['price'], 2); ?></td>
                        <td>
                            <!-- This dynamically shows "Available" in green or "Unavailable" in red -->
                            <?php if ($product['is_available']): ?>
                                <span class="status-available">Available</span>
                            <?php else: ?>
                                <span class="status-unavailable">Unavailable</span>
                            <?php endif; ?>
                        </td>
                        <td class="actions">
                            <!-- These links are placeholders for the next phase (CRUD operations) -->
                            <a href="admin_edit_item.php?id=<?php echo $product['id']; ?>" class="btn-action btn-edit"><i class="fas fa-edit"></i> Edit</a>
                            <a href="admin_delete_item.php?id=<?php echo $product['id']; ?>" class="btn-action btn-delete"><i class="fas fa-trash"></i> Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <!-- The PHP loop ends here -->
                <?php else: ?>
                    <tr><td colspan="5" style="text-align:center;">No menu items found. Click "Add New Item" to get started.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>