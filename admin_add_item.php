<?php
session_start();

// Security Check: Redirect non-admins away.
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add New Menu Item - KLEver Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Lora:wght@700&display=swap" rel="stylesheet">
  <!-- We link to the same stylesheet for a consistent look -->
  <link rel="stylesheet" href="admin_dashboard_styles.css">
  <style>
    /* Additional styles for the form */
    .form-container {
        background-color: #fff;
        padding: 2rem;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }
    .form-group {
        margin-bottom: 1.5rem;
    }
    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: #333;
    }
    .form-group input, .form-group select, .form-group textarea {
        width: 100%;
        padding: 0.8rem;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-family: 'Poppins', sans-serif;
        font-size: 1rem;
    }
    .form-actions {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
    }
    .form-actions .btn {
        border: none;
        cursor: pointer;
    }
    .btn-cancel {
        background-color: #6c757d;
        color: white;
    }
  </style>
</head>
<body>
<div class="admin-wrapper">
    
    <?php include 'admin_sidebar.php'; ?>

    <!-- Main Content Area -->
    <div class="main-content">
        <div class="header-bar">
            <h1>Add New Menu Item</h1>
            <a href="admin_menu.php" class="btn btn-cancel"><i class="fas fa-times"></i> Cancel</a>
        </div>
        
        <div class="form-container">
            <!-- The form will submit its data to our new handler script -->
            <form action="admin_handle_add.php" method="POST">
                <div class="form-group">
                    <label for="name">Item Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="price">Price (₹)</label>
                    <input type="number" id="price" name="price" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="image_url">Image URL</label>
                    <input type="url" id="image_url" name="image_url" required>
                </div>
                <div class="form-group">
                    <label for="is_available">Status</label>
                    <select id="is_available" name="is_available">
                        <option value="1">Available</option>
                        <option value="0">Unavailable</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="is_featured">Featured on Homepage?</label>
                    <select id="is_featured" name="is_featured">
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Save Item</button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>