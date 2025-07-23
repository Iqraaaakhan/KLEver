<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'klever_db');
if ($conn->connect_error) { 
    die("Connection Failed: " . $conn->connect_error); 
}

// This query from your code is correct. It sorts by active status first.
$products_result = $conn->query("SELECT * FROM products ORDER BY is_active DESC, name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Menu Management - KLEver Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Lora:wght@700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="admin_dashboard_styles.css"> 
</head>
<body>
<div class="admin-wrapper">
    
    <?php include 'admin_sidebar.php'; ?>

    <div class="main-content">
        <div class="header-bar">
            <h1>Menu Management</h1>
            <a href="admin_add_item.php" class="btn btn-success"><i class="fas fa-plus"></i> Add New Item</a>
        </div>
        
        <p class="page-description">Here you can view, add, edit, and manage all the food items available in the canteen.</p>

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
                    <?php while($product = $products_result->fetch_assoc()): ?>
                        <tr class="<?php echo ($product['is_active'] == 0) ? 'inactive-row' : ''; ?>">
                            <td><img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="item-image"></td>
                            <td><strong><?php echo htmlspecialchars($product['name']); ?></strong></td>
                            <td>₹<?php echo number_format($product['price'], 2); ?></td>
                            <td>
                                <!-- THIS IS THE NEW, COMBINED STATUS LOGIC -->
                                <?php
                                    if ($product['is_active'] == 1) {
                                        // If the item is Active, check if it's Available for today.
                                        if ($product['is_available'] == 1) {
                                            echo '<span class="status-available">Available</span>';
                                        } else {
                                            echo '<span class="status-unavailable">Unavailable</span>';
                                        }
                                    } else {
                                        // If the item is not Active, it's considered Inactive.
                                        echo '<span class="status-inactive">Inactive</span>';
                                    }
                                ?>
                            </td>
                            <td class="actions">
                                <!-- YOUR BUTTON LOGIC IS PERFECT AND IS PRESERVED HERE -->
                                <?php if ($product['is_active']): ?>
                                    <a href="admin_edit_item.php?id=<?php echo $product['id']; ?>" class="btn-action btn-edit"><i class="fas fa-edit"></i> Edit</a>                                    
                                    <a href="admin_delete_item.php?id=<?php echo $product['id']; ?>" class="btn-action btn-delete" onclick="return confirm('Are you sure you want to mark this item as inactive?');"><i class="fas fa-trash"></i> Delete</a>
                                <?php else: ?>
                                    <a href="admin_restore_item.php?id=<?php echo $product['id']; ?>" class="btn-action btn-restore"><i class="fas fa-undo"></i> Restore</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" style="text-align:center;">No menu items found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>