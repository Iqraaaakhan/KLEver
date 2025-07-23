<?php
session_start();

// Security Check: Redirect non-admins away.
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

// 1. Connect to the database.
$conn = new mysqli('localhost', 'root', '', 'klever_db');
if ($conn->connect_error) { 
    die("Connection Failed: " . $conn->connect_error); 
}

// 2. Check if a valid item ID was provided in the URL.
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: admin_menu.php");
    exit;
}

$item_id = $_GET['id'];

// 3. Fetch the existing data for this specific item.
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $item_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    // If no item with that ID exists, redirect away.
    header("Location: admin_menu.php");
    exit;
}

// 4. Store the item's data in a variable.
$product = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Menu Item - KLEver Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- We link to the same stylesheets for a consistent look -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Lora:wght@700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="admin_dashboard_styles.css">
  <!-- Re-using the same form styles from the 'add' page -->
  <style>
    .form-container { background-color: #fff; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
    .form-group { margin-bottom: 1.5rem; }
    .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 600; }
    .form-group input, .form-group select { width: 100%; padding: 0.8rem; border: 1px solid #ccc; border-radius: 5px; font-size: 1rem; }
    .form-actions { display: flex; gap: 1rem; justify-content: flex-end; }
    .form-actions .btn { border: none; cursor: pointer; }
    .btn-cancel { background-color: #6c757d; color: white; }
  </style>
</head>
<body>
<div class="admin-wrapper">
    
    <?php include 'admin_sidebar.php'; ?>

    <div class="main-content">
        <div class="header-bar">
            <h1>Edit Menu Item</h1>
            <a href="admin_menu.php" class="btn btn-cancel"><i class="fas fa-times"></i> Cancel</a>
        </div>
        
        <div class="form-container">
            <!-- The form submits to our new handler script -->
            <form action="admin_handle_edit.php" method="POST">
                <!-- CRITICAL: This hidden input sends the ID of the item we are editing -->
                <input type="hidden" name="id" value="<?php echo $product['id']; ?>">

                <div class="form-group">
                    <label for="name">Item Name</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="price">Price (₹)</label>
                    <input type="number" id="price" name="price" step="0.01" value="<?php echo htmlspecialchars($product['price']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="image_url">Image URL</label>
                    <input type="text" id="image_url" name="image_url" value="<?php echo htmlspecialchars($product['image_url']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="is_available">Status</label>
                    <select id="is_available" name="is_available">
                        <!-- We use PHP to select the correct current option -->
                        <option value="1" <?php if($product['is_available'] == 1) echo 'selected'; ?>>Available</option>
                        <option value="0" <?php if($product['is_available'] == 0) echo 'selected'; ?>>Unavailable</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="is_featured">Featured on Homepage?</label>
                    <select id="is_featured" name="is_featured">
                        <option value="0" <?php if($product['is_featured'] == 0) echo 'selected'; ?>>No</option>
                        <option value="1" <?php if($product['is_featured'] == 1) echo 'selected'; ?>>Yes</option>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Update Item</button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>