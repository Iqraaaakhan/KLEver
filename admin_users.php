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

// 2. Prepare the base query.
// This query is designed to get all regular users (type != 1) and exclude the admin.
$query = "SELECT id, username, email, phone FROM user WHERE type != 1";

// 3. (Professional Feature) Add search functionality.
$search_term = '';
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_term = $conn->real_escape_string($_GET['search']);
    // Add a WHERE clause to search by username or email
    $query .= " AND (username LIKE '%$search_term%' OR email LIKE '%$search_term%')";
}

$query .= " ORDER BY id DESC"; // Show newest users first

$users_result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Management - KLEver Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Lora:wght@700&display=swap" rel="stylesheet">
  <!-- We link to the same, single stylesheet for a consistent admin panel -->
  <link rel="stylesheet" href="admin_dashboard_styles.css"> 
  <style>
    /* Additional styles specific to the search bar on this page */
    .search-bar {
        margin-bottom: 2rem;
        display: flex;
        box-shadow: 0 2px 8px rgba(0,0,0,0.07);
        border-radius: 6px;
        overflow: hidden;
    }
    .search-bar input {
        flex-grow: 1;
        border: 1px solid #ddd;
        padding: 0.8rem;
        font-size: 1rem;
        border-right: none;
    }
    .search-bar button {
        border: none;
        background-color: #555;
        color: white;
        padding: 0 1.5rem;
        cursor: pointer;
        font-size: 1.1rem;
    }
  </style>
</head>
<body>
<div class="admin-wrapper">
    
    <?php 
    // This includes your existing sidebar. It will correctly highlight "Users" as active.
    include 'admin_sidebar.php'; 
    ?>

    <!-- Main Content Area -->
    <div class="main-content">
        <div class="header-bar">
            <h1>User Management</h1>
        </div>
        
        <p class="page-description">A list of all registered student accounts on the KLEver platform.</p>

        <!-- (Professional Feature) Search Form -->
        <form action="admin_users.php" method="GET" class="search-bar">
            <input type="text" name="search" placeholder="Search by username or email..." value="<?php echo htmlspecialchars($search_term); ?>">
            <button type="submit"><i class="fas fa-search"></i></button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Phone</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($users_result && $users_result->num_rows > 0): ?>
                    <?php while($user = $users_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($user['username']); ?></strong></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['phone']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4" style="text-align:center;">
                        <?php if (!empty($search_term)): ?>
                            No users found matching your search.
                        <?php else: ?>
                            No users have registered yet.
                        <?php endif; ?>
                    </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>